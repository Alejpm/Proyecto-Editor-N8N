let nodes = [];
let edges = [];
let nodeCounter = 1;
let selectedNode = null;

function addNode(type) {
    const canvas = document.getElementById("canvas");

    const node = document.createElement("div");
    node.className = "node";
    node.innerText = type;
    node.style.left = "100px";
    node.style.top = "100px";

    const id = "n" + nodeCounter++;
    node.dataset.id = id;
    node.dataset.type = type;

    node.onmousedown = drag;

    node.onclick = () => {
        if (selectedNode && selectedNode !== id) {
            edges.push({ from: selectedNode, to: id });
            selectedNode = null;
            alert("Conexión creada");
        } else {
            selectedNode = id;
        }
    };

    canvas.appendChild(node);

    nodes.push({
        id,
        type,
        x: 100,
        y: 100,
        config: {}
    });
}

function drag(e) {
    const node = e.target;
    let shiftX = e.clientX - node.getBoundingClientRect().left;
    let shiftY = e.clientY - node.getBoundingClientRect().top;

    function moveAt(pageX, pageY) {
        node.style.left = pageX - shiftX + 'px';
        node.style.top = pageY - shiftY + 'px';
    }

    function onMouseMove(e) {
        moveAt(e.pageX, e.pageY);
    }

    document.addEventListener('mousemove', onMouseMove);

    node.onmouseup = function() {
        document.removeEventListener('mousemove', onMouseMove);
        node.onmouseup = null;

        const id = node.dataset.id;
        const n = nodes.find(n => n.id === id);
        n.x = parseInt(node.style.left);
        n.y = parseInt(node.style.top);
    };
}

function saveWorkflow() {
    const name = prompt("Nombre del workflow:");

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
    .then(data => alert("Guardado ID: " + data.workflow_id));
}

function runWorkflow() {
    const id = prompt("ID del workflow a ejecutar:");
    fetch("api.php?action=run&id=" + id)
    .then(res => res.json())
    .then(data => console.log(data));
}

