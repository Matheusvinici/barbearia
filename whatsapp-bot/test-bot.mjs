import {
    makeWASocket,
    useMultiFileAuthState,
    fetchLatestBaileysVersion,
    DisconnectReason,
} from '@whiskeysockets/baileys';

const { version } = await fetchLatestBaileysVersion();
console.log('Baileys version:', version);

const { state, saveCreds } = await useMultiFileAuthState('auth_test');

const sock = makeWASocket({
    version,
    auth: state,
    printQRInTerminal: true,
    logger: console,
    browser: ['Chrome', 'Linux', '136.0.7103.92'],
});

sock.ev.on('creds.update', saveCreds);

sock.ev.on('connection.update', (update) => {
    console.log('Connection update:', JSON.stringify(update, null, 2));
});

setTimeout(() => {
    console.log('Socket WS state:', sock.ws?.readyState);
    process.exit(0);
}, 15000);
