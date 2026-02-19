<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ERP Visual</title>
<style>
body { font-family: Arial; display:flex; height:100vh; margin:0; }
#toolbar { width:250px; background:#f4f4f4; padding:10px; }
#canvas { flex:1; position:relative; background:#eef1f7; }
.node {
    position:absolute;
    width:160px;
    padding:10px;
    background:white;
    border:1px solid #ccc;
    border-radius:8px;
    cursor:move;
}
button { margin:5px 0; width:100%; }
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

