// Servidor de avisos en tiempo real. PHP le avisa por HTTP (solo desde este equipo y con secreto)
// y él reenvía "rankingActualizado" a los visores suscriptos a ese juego.
const http = require('http');
const { WebSocketServer } = require('ws');
const SECRETO = process.env.WS_SECRETO || 'cambiame';   // el mismo valor que WS_SECRETO en api/db.php
const PUERTO = +process.env.PORT || 8081;
const local = a => ['127.0.0.1', '::1', '::ffff:127.0.0.1'].includes(a);

const server = http.createServer((req, res) => {
  if (req.method === 'POST' && req.url === '/notificar') {
    if (!local(req.socket.remoteAddress) || req.headers['x-secreto'] !== SECRETO) { res.writeHead(403); return res.end(); }
    let b = '';
    req.on('data', c => { b += c; if (b.length > 1024) req.destroy(); });
    req.on('end', () => {
      try {
        const id = JSON.parse(b).juegoId;
        if (!Number.isInteger(id)) throw new Error('juegoId inválido');
        const msg = JSON.stringify({ tipo: 'rankingActualizado', juegoId: id });
        wss.clients.forEach(c => { if (c.readyState === 1 && c.juegoId === id) c.send(msg); });
        res.writeHead(204); res.end();
      } catch { res.writeHead(400); res.end(); }
    });
    return;
  }
  res.writeHead(404); res.end();
});

// Los clientes solo pueden suscribirse a un juego; no pueden anunciar resultados.
const wss = new WebSocketServer({ server, maxPayload: 1024 });
wss.on('connection', ws => {
  ws.vivo = true;
  ws.on('pong', () => { ws.vivo = true; });
  ws.on('message', d => {
    try { const m = JSON.parse(d); if (m.tipo === 'suscribir' && Number.isInteger(m.juegoId)) ws.juegoId = m.juegoId; } catch {}
  });
});
setInterval(() => wss.clients.forEach(c => { if (!c.vivo) return c.terminate(); c.vivo = false; c.ping(); }), 30000);
server.listen(PUERTO, () => console.log('Avisos en tiempo real en el puerto ' + PUERTO));
