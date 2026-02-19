<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ERP Visual</title>
<style>
:root {
    --bg-main: #f5f7fb;
    --bg-panel: #ffffff;
    --primary: #4f46e5;
    --primary-light: #6366f1;
    --danger: #ef4444;
    --success: #16a34a;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border: #e2e8f0;
}

body {
    margin: 0;
    display: flex;
    height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--bg-main);
    color: var(--text-main);
}

/* ===== Toolbar ===== */
#toolbar {
    width: 260px;
    background: var(--bg-panel);
    padding: 20px;
    box-shadow: 4px 0 20px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

#toolbar h2 {
    margin: 0 0 10px 0;
    font-size: 18px;
}

button {
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

button:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

button:active {
    transform: translateY(0);
}

/* ===== Canvas ===== */
#canvas {
    flex: 1;
    position: relative;
    background: 
        radial-gradient(circle at 1px 1px, #dbeafe 1px, transparent 1px);
    background-size: 30px 30px;
}

/* ===== Nodes ===== */
.node {
    position: absolute;
    width: 180px;
    padding: 15px;
    border-radius: 14px;
    background: linear-gradient(145deg, #ffffff, #f1f5f9);
    border: 1px solid var(--border);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    cursor: grab;
    transition: all 0.2s ease;
    font-weight: 600;
}

.node:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    transform: scale(1.02);
}

.node.selected {
    border: 2px solid var(--primary);
}

/* ===== Node Types Colors ===== */

.node[data-type="crear_pedido"] { border-left: 6px solid #6366f1; }
.node[data-type="verificar_cliente"] { border-left: 6px solid #f59e0b; }
.node[data-type="verificar_stock"] { border-left: 6px solid #ef4444; }
.node[data-type="calcular_total"] { border-left: 6px solid #8b5cf6; }
.node[data-type="guardar_pedido"] { border-left: 6px solid #16a34a; }
.node[data-type="generar_factura"] { border-left: 6px solid #0ea5e9; }

/* ===== Execution Animation ===== */
.node.running {
    animation: pulse 0.8s infinite alternate;
}

@keyframes pulse {
    from { box-shadow: 0 0 0 rgba(79,70,229,0.3); }
    to { box-shadow: 0 0 25px rgba(79,70,229,0.5); }
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

<div id="canvas"></div>

<script src="assets/js/editor.js"></script>
</body>
</html>

