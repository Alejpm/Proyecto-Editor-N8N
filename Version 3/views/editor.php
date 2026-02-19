<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ERP Visual</title>

<style>
:root {
    --primary: #4f46e5;
    --border: #e2e8f0;
}

body {
    margin: 0;
    display: flex;
    height: 100vh;
    font-family: Arial, sans-serif;
    background: #f3f4f6;
}

/* Toolbar */
#toolbar {
    width: 250px;
    background: white;
    padding: 20px;
    box-shadow: 4px 0 15px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

button {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid var(--border);
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    background: var(--primary);
    color: white;
}

/* Canvas */
#canvas {
    flex: 1;
    position: relative;
    background: radial-gradient(circle at 1px 1px, #dbeafe 1px, transparent 1px);
    background-size: 30px 30px;
    overflow: hidden;
}

/* SVG */
#connections {
    position: absolute;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.connection-path {
    fill: none;
    stroke-width: 3;
}

/* Nodes */
.node {
    position: absolute;
    width: 170px;
    padding: 15px;
    border-radius: 12px;
    background: white;
    border: 1px solid var(--border);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    cursor: grab;
    user-select: none;
    font-weight: bold;
}

/* ================= HUD ================= */

#hud {
    position: absolute;
    bottom: 20px;
    left: 20px;
    width: 420px;
    max-height: 300px;
    background: #111827;
    color: white;
    border-radius: 14px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-size: 13px;
}

#hudHeader {
    padding: 10px 15px;
    background: #1f2937;
    display: flex;
    justify-content: space-between;
    font-weight: bold;
}

#hudStatus.idle { color: #9ca3af; }
#hudStatus.running { color: #3b82f6; }
#hudStatus.success { color: #10b981; }
#hudStatus.error { color: #ef4444; }

#hudLog {
    padding: 10px;
    overflow-y: auto;
    flex: 1;
}

.hud-line {
    margin-bottom: 6px;
}

.hud-error {
    color: #f87171;
}

.hud-success {
    color: #4ade80;
}
</style>
</head>

<body>

<div id="toolbar">
    <button onclick="addNode('crear_pedido')">Crear Pedido</button>
    <button onclick="addNode('verificar_cliente')">Verificar Cliente</button>
    <button onclick="addNode('verificar_stock')">Verificar Stock</button>
    <button onclick="addNode('calcular_total')">Calcular Total</button>
    <button onclick="addNode('guardar_pedido')">Guardar Pedido</button>
    <button onclick="addNode('generar_factura')">Generar Factura</button>
    <hr>
    <button onclick="saveWorkflow()">Guardar Workflow</button>
    <button onclick="runWorkflow()">Ejecutar Workflow</button>
</div>

<div id="canvas">
    <svg id="connections"></svg>

    <div id="hud">
        <div id="hudHeader">
            <span id="hudStatus" class="idle">● Idle</span>
            <span id="hudCounter">0 nodos</span>
        </div>
        <div id="hudLog"></div>
    </div>
</div>

<script src="assets/js/editor.js"></script>

</body>
</html>



