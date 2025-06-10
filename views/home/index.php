<div>
    <input type="number" id="numberInput" value="42"/>
    <button id="sendBtn">Відправити</button>
</div>
<div id="result"></div>

<script>
    document.getElementById('sendBtn').addEventListener('click', function () {
        const number = document.getElementById('numberInput').value;
        fetch('/test/test', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({number})
        })
            .then(r => r.json())
            .then(data => {
                document.getElementById('result').textContent = JSON.stringify(data);
            })
            .catch(e => {
                document.getElementById('result').textContent = 'Помилка: ' + e;
            });
    });
</script>
