import dotenv from 'dotenv';
dotenv.config();

import {
    makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
} from '@whiskeysockets/baileys';
import qrcode from 'qrcode';
import QRTerminal from 'qrcode-terminal';
import express from 'express';
import axios from 'axios';
import pino from 'pino';

const APP_URL = process.env.APP_URL || 'http://localhost:8000';
const PORT = process.env.PORT || 3000;

const app = express();
app.use(express.json());

const sessions = {};

const STEPS = {
    INIT: 'init',
    BARBER: 'barber',
    DAY: 'day',
    TIME: 'time',
    SERVICE: 'service',
    CONFIRM: 'confirm',
};

function formatMenu(title, items, extra = '') {
    let txt = `*${title}*\n`;
    if (extra) txt += `\n${extra}\n`;
    txt += '\n' + items.map((item, i) => `*${i + 1}* - ${item.label}`).join('\n');
    txt += '\n\n*0* - ❌ Sair';
    return txt;
}

process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err);
});
process.on('unhandledRejection', (err) => {
    console.error('Unhandled Rejection:', err);
});

async function startBot() {
    const { version } = await fetchLatestBaileysVersion();
    const { state, saveCreds } = await useMultiFileAuthState('auth_baileys');

    const sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: false,
        logger: pino({ level: 'warn' }),
        browser: ['Barbearia Bot', 'Chrome', '136.0.7103.92'],
        markOnlineOnConnect: false,
        connectTimeoutMs: 60000,
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', ({ connection, lastDisconnect, qr }) => {
        if (qr) {
            QRTerminal.generate(qr, { small: true });
            qrcode.toFile('/tmp/whatsapp-qr.png', qr, { type: 'png', width: 512 }, () => {
                console.log('\n📱 QR code saved as /tmp/whatsapp-qr.png');
            });
            qrcode.toFile('../public/storage/bot-qr.png', qr, { type: 'png', width: 512 }, () => {
                console.log('📱 QR also saved for Laravel admin');
            });
        }
        if (connection === 'open') {
            console.log('WhatsApp Bot is ready!');
        }
        if (connection === 'close') {
            const shouldReconnect =
                lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
            if (shouldReconnect) {
                const hasSession = state.creds?.registered;
                const delay = hasSession ? 1000 : 30000;
                console.log(`Disconnected, reconnecting in ${delay/1000}s...`);
                setTimeout(() => startBot(), delay);
            } else {
                console.log('Logged out. Delete auth_baileys folder and restart.');
            }
        }
    });

    global.sock = sock;

    sock.ev.on('messages.upsert', async ({ messages }) => {
        try {
            const msg = messages[0];
            if (!msg || msg.key.fromMe) return;

            const from = msg.key.remoteJid;
            const text =
                msg.message?.conversation ||
                msg.message?.extendedTextMessage?.text ||
                '';

            if (!text) return;

            const input = text.trim();

            if (!sessions[from]) {
                sessions[from] = { step: STEPS.INIT };
            }

            const session = sessions[from];

            if (input === '0') {
                delete sessions[from];
                await sock.sendMessage(from, {
                    text: '✅ Atendimento encerrado. Mande qualquer mensagem quando quiser voltar.',
                });
                return;
            }

            if (input.toLowerCase() === 'voltar' && session.step !== STEPS.BARBER) {
                const prev = { day: STEPS.BARBER, time: STEPS.DAY, service: STEPS.TIME };
                session.step = prev[session.step] || STEPS.INIT;
                const redo = {
                    [STEPS.BARBER]: () => showBarbers(sock, from, session),
                    [STEPS.DAY]: () => showDays(sock, from, session),
                    [STEPS.TIME]: () => showTimes(sock, from, session),
                };
                if (redo[session.step]) await redo[session.step]();
                return;
            }

            switch (session.step) {
                case STEPS.INIT:
                    await handleInit(sock, from, session);
                    break;
                case STEPS.BARBER:
                    await handleBarber(sock, from, input, session);
                    break;
                case STEPS.DAY:
                    await handleDay(sock, from, input, session);
                    break;
                case STEPS.TIME:
                    await handleTime(sock, from, input, session);
                    break;
                case STEPS.SERVICE:
                    await handleService(sock, from, input, session);
                    break;
                case STEPS.CONFIRM:
                    await handleConfirm(sock, from, input, session);
                    break;
            }
        } catch (err) {
            console.error('Error processing message:', err.message);
            try {
                await msg?.key?.remoteJid && sock.sendMessage(msg.key.remoteJid, {
                    text: '❌ Ocorreu um erro. Tente novamente.',
                });
            } catch (_) {}
        }
    });
}

async function showBarbers(sock, from, session) {
    try {
        const { data } = await axios.get(`${APP_URL}/api/bot/barbeiros`);
        if (!data.length) {
            await sock.sendMessage(from, {
                text: 'Desculpe, não temos barbeiros disponíveis no momento.',
            });
            return false;
        }
        session.barbers = data;
        session.step = STEPS.BARBER;

        const items = data.map((b) => ({ label: `👨‍🦰 ${b.nome}`, id: b.id }));
        const msg = formatMenu('🪒 Selecione o Barbeiro', items, 'Responda com o número desejado:');
        await sock.sendMessage(from, { text: msg });
        return true;
    } catch (err) {
        console.error('Error showBarbers:', err.message);
        await sock.sendMessage(from, {
            text: 'Desculpe, ocorreu um erro. Tente novamente mais tarde.',
        });
        return false;
    }
}

async function showDays(sock, from, session) {
    try {
        const { data } = await axios.get(`${APP_URL}/api/bot/dias-disponiveis`, {
            params: { barbeiro_id: session.barber.id },
        });
        if (!data.length) {
            await sock.sendMessage(from, {
                text: '😕 Não há dias disponíveis para este barbeiro.',
            });
            return false;
        }
        session.dias = data;
        session.step = STEPS.DAY;

        const items = data.map((d) => ({ label: `📅 ${d.label}`, id: d.data }));
        const msg = formatMenu(`📅 Dias - ${session.barber.nome}`, items, 'Responda com o número desejado:');
        await sock.sendMessage(from, { text: msg });
        return true;
    } catch (err) {
        console.error('Error showDays:', err.message);
        await sock.sendMessage(from, { text: 'Erro ao buscar dias. Tente novamente.' });
        return false;
    }
}

async function showTimes(sock, from, session) {
    try {
        const { data } = await axios.get(`${APP_URL}/api/bot/horarios`, {
            params: { barbeiro_id: session.barber.id, data: session.dia.data },
        });
        if (!data.length) {
            await sock.sendMessage(from, {
                text: '😕 Não há horários disponíveis para esta data.',
            });
            return false;
        }
        session.horarios = data;
        session.step = STEPS.TIME;

        const items = data.map((h) => ({ label: `🕐 ${h}`, id: h }));
        const msg = formatMenu('🕐 Horários Disponíveis', items, `Data: *${session.dia.label}*\n\nResponda com o número desejado:`);
        await sock.sendMessage(from, { text: msg });
        return true;
    } catch (err) {
        console.error('Error showTimes:', err.message);
        await sock.sendMessage(from, { text: 'Erro ao buscar horários. Tente novamente.' });
        return false;
    }
}

async function showServices(sock, from, session) {
    try {
        const { data } = await axios.get(`${APP_URL}/api/bot/servicos`);
        if (!data.length) {
            await sock.sendMessage(from, {
                text: '😕 Não há serviços disponíveis no momento.',
            });
            return false;
        }
        session.servicos = data;
        session.step = STEPS.SERVICE;

        const items = data.map((s) => ({
            label: `💈 ${s.nome} - R$ ${parseFloat(s.preco).toFixed(2)}`,
            id: s.id,
        }));
        const msg = formatMenu('💈 Selecione o Serviço', items, 'Responda com o número desejado:');
        await sock.sendMessage(from, { text: msg });
        return true;
    } catch (err) {
        console.error('Error showServices:', err.message);
        await sock.sendMessage(from, { text: 'Erro ao buscar serviços. Tente novamente.' });
        return false;
    }
}

function getSelected(items, input) {
    const idx = parseInt(input);
    if (isNaN(idx) || idx < 1 || idx > items.length) return null;
    return items[idx - 1];
}

function getSelectedRaw(items, input) {
    const idx = parseInt(input);
    if (isNaN(idx) || idx < 1 || idx > items.length) return null;
    return { item: items[idx - 1], index: idx - 1 };
}

async function handleInit(sock, from, session) {
    delete sessions[from];
    sessions[from] = { step: STEPS.INIT };
    await showBarbers(sock, from, sessions[from]);
}

async function handleBarber(sock, from, input, session) {
    const sel = getSelected(session.barbers, input);
    if (!sel) {
        await sock.sendMessage(from, { text: '❌ Número inválido. Digite o número do barbeiro desejado.' });
        return;
    }
    session.barber = sel;
    await showDays(sock, from, session);
}

async function handleDay(sock, from, input, session) {
    const sel = getSelectedRaw(session.dias, input);
    if (!sel) {
        await sock.sendMessage(from, { text: '❌ Número inválido. Digite o número do dia desejado.' });
        return;
    }
    session.dia = sel.item;
    await showTimes(sock, from, session);
}

async function handleTime(sock, from, input, session) {
    const sel = getSelectedRaw(session.horarios, input);
    if (!sel) {
        await sock.sendMessage(from, { text: '❌ Número inválido. Digite o número do horário desejado.' });
        return;
    }
    session.hora = sel.item;
    await showServices(sock, from, session);
}

async function handleService(sock, from, input, session) {
    const sel = getSelectedRaw(session.servicos, input);
    if (!sel) {
        await sock.sendMessage(from, { text: '❌ Número inválido. Digite o número do serviço desejado.' });
        return;
    }
    session.servico = sel.item;
    session.step = STEPS.CONFIRM;

    const msg = `✅ *Confirme seu agendamento:*\n\n👨‍🦰 *Barbeiro:* ${session.barber.nome}\n📅 *Data:* ${session.dia.label}\n🕐 *Horário:* ${session.hora}\n💈 *Serviço:* ${session.servico.nome}\n💰 *Valor:* R$ ${parseFloat(session.servico.preco).toFixed(2)}\n\n*1* - ✅ Confirmar\n*2* - 🔙 Voltar\n*0* - ❌ Sair`;

    await sock.sendMessage(from, { text: msg });
}

async function handleConfirm(sock, from, input, session) {
    if (input === '1') {
        try {
            const telefone = from.replace('@s.whatsapp.net', '').replace('@lid', '');
            const payload = {
                barbeiro_id: session.barber.id,
                servico_id: session.servico.id,
                data: session.dia.data,
                hora: session.hora,
                cliente_nome: 'Cliente WhatsApp',
                cliente_telefone: telefone,
                whatsapp_id: telefone,
            };

            const { data } = await axios.post(`${APP_URL}/api/bot/agendar`, payload);

            if (data.success) {
                await sock.sendMessage(from, {
                    text: `🎉 *Agendamento confirmado!*\n\n📅 *Data:* ${data.data}\n🕐 *Horário:* ${data.hora}\n👨‍🦰 *Barbeiro:* ${data.barbeiro}\n💈 *Serviço:* ${data.servico}\n💰 *Valor:* R$ ${parseFloat(data.preco).toFixed(2)}\n\n🕐 Enviaremos um lembrete 1h antes!\n\nDigite *1* para novo agendamento ou *0* para sair.`,
                });
                delete sessions[from];
            } else {
                await sock.sendMessage(from, {
                    text: '❌ Erro ao confirmar agendamento. Tente novamente.',
                });
                session.step = STEPS.SERVICE;
                await showServices(sock, from, session);
            }
        } catch (err) {
            console.error('Error creating appointment:', err.response?.data || err.message);
            await sock.sendMessage(from, {
                text: '❌ Erro ao confirmar agendamento. Tente novamente.',
            });
            session.step = STEPS.SERVICE;
            await showServices(sock, from, session);
        }
    } else if (input === '2') {
        session.step = STEPS.SERVICE;
        await showServices(sock, from, session);
    } else if (input === '1' && !session.servico) {
        delete sessions[from];
        sessions[from] = { step: STEPS.INIT };
        await showBarbers(sock, from, sessions[from]);
    } else {
        await sock.sendMessage(from, {
            text: 'Responda com *1* para confirmar, *2* para voltar ou *0* para sair.',
        });
    }
}

setInterval(async () => {
    try {
        const { data } = await axios.get(`${APP_URL}/api/bot/lembretes`);
        if (!data.length) return;

        const sock = global.sock;
        if (!sock) return;

        for (const ag of data) {
            const numero = ag.cliente_telefone.replace(/\D/g, '');
            const chatId = `${numero}@s.whatsapp.net`;
            const msg = `✂️ *Lembrete de Agendamento*\n\nOlá *${ag.cliente_nome}*! Lembramos que você tem um horário marcado hoje às *${ag.hora}* com *${ag.barbeiro_nome}*.\n\nServiço: ${ag.servicos}\n\nTe esperamos! 🫡`;
            await sock.sendMessage(chatId, { text: msg });
            console.log(`Reminder sent to ${ag.cliente_nome} (${ag.cliente_telefone})`);
        }
    } catch (err) {
        // silent
    }
}, 60000);

app.get('/health', (req, res) => {
    const sock = global.sock;
    const authed = sock?.authState?.creds?.registered === true;
    res.json({
        status: 'ok',
        connected: !!sock?.ws?.isOpen,
        authenticated: authed,
        has_qr: authed ? false : true,
    });
});

app.post('/pair', express.json(), async (req, res) => {
    try {
        const { phone } = req.body;
        if (!phone) return res.status(400).json({ error: 'Phone number required' });

        const sock = global.sock;
        if (!sock) return res.status(503).json({ error: 'Bot not ready' });

        const cleaned = phone.replace(/\D/g, '');
        const pairingCode = await sock.requestPairingCode(cleaned);

        res.json({
            success: true,
            pairing_code: pairingCode,
            message: `Código de pareamento: ${pairingCode}`,
        });
    } catch (err) {
        console.error('Pairing error:', err.message);
        res.status(500).json({ error: err.message });
    }
});

app.listen(PORT, () => {
    console.log(`Bot server running on port ${PORT}`);
});

startBot();
