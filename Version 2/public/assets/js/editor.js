let nodes = [];
let edges = [];
let nodeCounter = 1;
let selectedNode = null;

/* ===============================
   CREAR NODO
================================= */
function addNode(type) {

    const canvas = document.getElementById("canvas");

    const node = document.createElement("div");
    node.className = "node";
    node.innerHTML = `<strong>${type.replaceAll("_", " ")}</strong>`;
    node.style.left = "100px";
    node.style.top = "100px";

    const id = "n" + nodeCounter++;
    node.dataset.id = id;
    node.dataset.type = type;

    node.addEventListener("mousedown", drag);

    node.addEventListener("click", (e) => {

        e.stopPropagation();

        if (selectedNode && selectedNode !== id) {
            edges.push({ from: selectedNode, to: id });
            document.querySelector(`[data-id='${selectedNode}']`)
                .classList.remove("selected");

            selectedNode = null;
            alert("Conexión creada");
        } else {
            selectedNode = id;
            node.classList.add("selected");
        }
    });

    canvas.appendChild(node);

    nodes.push({
        id,
        type,
        x: 100,
        y: 100,
        config: {}
    });
}

/* ===============================
   DRAG CORREGIDO (SIN SALTOS)
================================= */
function drag(e) {

    if (e.target.closest("button")) return;

    const node = e.currentTarget;
    const canvas = document.getElementById("canvas");

    const canvasRect = canvas.getBoundingClientRect();
    const nodeRect = node.getBoundingClientRect();

    const shiftX = e.clientX - nodeRect.left;
    const shiftY = e.clientY - nodeRect.top;

    function moveAt(clientX, clientY) {
        node.style.left = (clientX - canvasRect.left - shiftX) + 'px';
        node.style.top  = (clientY - canvasRect.top  - shiftY) + 'px';
    }

    function onMouseMove(e) {
        moveAt(e.clientX, e.clientY);
    }

    document.addEventListener('mousemove', onMouseMove);

    document.addEventListener('mouseup', function stopDrag() {

        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', stopDrag);

        const id = node.dataset.id;
        const n = nodes.find(n => n.id === id);

        n.x = parseInt(node.style.left);
        n.y = parseInt(node.style.top);
    });
}

/* ===============================
   GUARDAR WORKFLOW
================================= */
function saveWorkflow() {

    const name = prompt("Nombre del workflow:");

    if (!name) return;

    fetch("api.php?action=save", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            name,
            nodes,
            edges
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.ok) {
            alert("Workflow guardado con ID: " + data.workflow_id);
        } else {
            alert("Error: " + data.error);
        }

    })
    .catch(err => console.error(err));
}

/* ===============================
   EJECUTAR WORKFLOW
================================= */
function runWorkflow() {

    const id = prompt("ID del workflow a ejecutar:");
    if (!id) return;

    fetch("api.php?action=run&id=" + id)
    .then(res => res.json())
    .then(data => {

        animateExecution(data);

        console.log("Resultado:", data);

    })
    .catch(err => console.error(err));
}

/* ===============================
   ANIMACIÓN DE EJECUCIÓN
================================= */
function animateExecution(data) {

    const order = Object.keys(data);

    order.forEach((nodeId, index) => {

        const el = document.querySelector(`[data-id='${nodeId}']`);

        if (!el) return;

        setTimeout(() => {

            el.classList.add("running");

            setTimeout(() => {
                el.classList.remove("running");
            }, 700);

        }, index * 800);

    });
}

/* ===============================
   LIMPIAR SELECCIÓN SI CLICK FUERA
================================= */
document.addEventListener("click", () => {

    if (selectedNode) {
        const el = document.querySelector(`[data-id='${selectedNode}']`);
        if (el) el.classList.remove("selected");
        selectedNode = null;
    }

});


