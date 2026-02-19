let nodes = [];
let edges = [];
let nodeCounter = 1;
let selectedNode = null;
let currentWorkflowId = null;

const canvas = document.getElementById("canvas");
const svg = document.getElementById("connections");

/* ===============================
   COLORES
================================= */
function getColor(type) {
    const colors = {
        crear_pedido: "#6366f1",
        verificar_cliente: "#f59e0b",
        verificar_stock: "#ef4444",
        calcular_total: "#8b5cf6",
        guardar_pedido: "#16a34a",
        generar_factura: "#0ea5e9"
    };
    return colors[type] || "#64748b";
}

/* ===============================
   CREAR NODO
================================= */
function addNode(type) {

    const node = document.createElement("div");
    node.className = "node";
    node.innerHTML = type.replaceAll("_", " ");
    node.style.left = "200px";
    node.style.top = "200px";

    const id = "n" + nodeCounter++;
    node.dataset.id = id;
    node.dataset.type = type;

    node.addEventListener("mousedown", drag);

    node.addEventListener("click", (e) => {
        e.stopPropagation();

        if (selectedNode && selectedNode !== id) {
            edges.push({ from: selectedNode, to: id });
            drawConnections();
            selectedNode = null;
        } else {
            selectedNode = id;
        }
    });

    canvas.appendChild(node);

    let config = {};

    if (type === "crear_pedido") {
        config = {
            cliente_id: parseInt(prompt("ID Cliente:")),
            producto_id: parseInt(prompt("ID Producto:")),
            cantidad: parseInt(prompt("Cantidad:"))
        };
    }

    nodes.push({ id, type, x: 200, y: 200, config });
}

/* ===============================
   DRAG
================================= */
function drag(e) {

    const node = e.currentTarget;
    const canvasRect = canvas.getBoundingClientRect();
    const nodeRect = node.getBoundingClientRect();

    const shiftX = e.clientX - nodeRect.left;
    const shiftY = e.clientY - nodeRect.top;

    function moveAt(clientX, clientY) {
        node.style.left = (clientX - canvasRect.left - shiftX) + "px";
        node.style.top  = (clientY - canvasRect.top  - shiftY) + "px";
        updatePosition(node.dataset.id);
        drawConnections();
    }

    function onMouseMove(e) {
        moveAt(e.clientX, e.clientY);
    }

    document.addEventListener("mousemove", onMouseMove);

    document.addEventListener("mouseup", function stop() {
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("mouseup", stop);
    });
}

function updatePosition(id) {
    const node = document.querySelector(`[data-id='${id}']`);
    const n = nodes.find(n => n.id === id);
    n.x = parseInt(node.style.left);
    n.y = parseInt(node.style.top);
}

/* ===============================
   DIBUJAR CONEXIONES
================================= */
function drawConnections() {

    svg.innerHTML = "";

    edges.forEach(edge => {

        const fromEl = document.querySelector(`[data-id='${edge.from}']`);
        const toEl = document.querySelector(`[data-id='${edge.to}']`);

        if (!fromEl || !toEl) return;

        const canvasRect = canvas.getBoundingClientRect();
        const fromRect = fromEl.getBoundingClientRect();
        const toRect = toEl.getBoundingClientRect();

        const x1 = fromRect.right - canvasRect.left;
        const y1 = fromRect.top + fromRect.height / 2 - canvasRect.top;

        const x2 = toRect.left - canvasRect.left;
        const y2 = toRect.top + toRect.height / 2 - canvasRect.top;

        const dx = Math.abs(x2 - x1) * 0.5;

        const path = document.createElementNS("http://www.w3.org/2000/svg", "path");

        path.setAttribute("d", `
            M ${x1} ${y1}
            C ${x1 + dx} ${y1},
              ${x2 - dx} ${y2},
              ${x2} ${y2}
        `);

        path.setAttribute("class", "connection-path");
        path.setAttribute("stroke", getColor(fromEl.dataset.type));

        svg.appendChild(path);
    });
}

/* ===============================
   GUARDAR
================================= */
function saveWorkflow() {

    const name = prompt("Nombre del workflow:");
    if (!name) return;

    fetch("api.php?action=save", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, nodes, edges })
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            currentWorkflowId = data.workflow_id;
            addLog("Workflow guardado correctamente", false);
        }
    });
}

/* ===============================
   EJECUTAR CON HUD
================================= */
function runWorkflow() {

    if (!currentWorkflowId) {
        addLog("Guarda primero el workflow.", true);
        return;
    }

    resetHUD();
    setStatus("running");

    fetch("api.php?action=run&id=" + currentWorkflowId)
    .then(res => res.json())
    .then(data => {

        let count = 0;
        let errorFound = false;

        Object.keys(data).forEach(nodeId => {

            count++;
            updateCounter(count);

            if (data[nodeId].error) {
                errorFound = true;
                addLog(`Nodo ${nodeId} ❌ ${data[nodeId].error}`, true);
                highlightNode(nodeId);
            } else {
                addLog(`Nodo ${nodeId} ✔ OK`, false);
            }
        });

        setStatus(errorFound ? "error" : "success");

    })
    .catch(() => {
        setStatus("error");
        addLog("Error inesperado", true);
    });
}

/* ===============================
   HUD FUNCTIONS
================================= */
function setStatus(state) {

    const status = document.getElementById("hudStatus");
    status.className = state;

    if (state === "running") status.innerText = "● Ejecutando";
    if (state === "success") status.innerText = "● Éxito";
    if (state === "error") status.innerText = "● Error";
    if (state === "idle") status.innerText = "● Idle";
}

function updateCounter(count) {
    document.getElementById("hudCounter").innerText = count + " nodos";
}

function addLog(message, isError) {

    const log = document.getElementById("hudLog");

    const line = document.createElement("div");
    line.className = "hud-line " + (isError ? "hud-error" : "hud-success");
    line.innerText = message;

    log.appendChild(line);
    log.scrollTop = log.scrollHeight;
}

function resetHUD() {
    document.getElementById("hudLog").innerHTML = "";
    updateCounter(0);
    setStatus("idle");
}

function highlightNode(nodeId) {

    const node = document.querySelector(`[data-id='${nodeId}']`);
    if (!node) return;

    node.style.border = "2px solid red";
    node.style.boxShadow = "0 0 20px red";

    setTimeout(() => {
        node.style.border = "1px solid var(--border)";
        node.style.boxShadow = "";
    }, 3000);
}



