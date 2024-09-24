<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barra de Progreso Animada</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        .progress-container {
            width: 300px;
        }
        .progress-bar {
            background-color: #e0e0e0;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .progress {
            width: 0;
            height: 100%;
            background-color: #5F50E4;
            border-radius: 10px;
            transition: width 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        .progress::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: linear-gradient(
                -45deg,
                rgba(255, 255, 255, 0.2) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0.2) 75%,
                transparent 75%,
                transparent
            );
            background-size: 50px 50px;
            animation: stripes 1s linear infinite;
            z-index: 1;
        }
        .progress::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: skew(-20deg);
            animation: shine 1.5s linear infinite;
        }
        @keyframes stripes {
            0% { background-position: 0 0; }
            100% { background-position: 50px 0; }
        }
        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        .progress-text {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }
        button {
            display: block;
            margin: 20px auto 0;
            padding: 10px 20px;
            background-color: #5F50E4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #4A3ED1;
        }
    </style>
</head>
<body>
    <div class="progress-container">
        <div class="progress-bar">
            <div class="progress" id="progress"></div>
        </div>
        <div class="progress-text" id="progressText">0%</div>
        <button onclick="incrementProgress()">Incrementar Progreso</button>
    </div>

    <script>
        let currentProgress = 0;
        const progressBar = document.getElementById('progress');
        const progressText = document.getElementById('progressText');

        function incrementProgress() {
            if (currentProgress < 100) {
                currentProgress += 10;
                updateProgress();
            }
        }

        function updateProgress() {
            progressBar.style.width = currentProgress + '%';
            progressText.textContent = currentProgress + '%';
        }
    </script>
</body>
</html>