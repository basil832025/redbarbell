<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Розклад тренерів</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table td button {
            height: 42px; /* фиксированная высота */
            min-width: 80px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.25rem 0.5rem;
        }

        .btn-sm { font-size: 0.85rem; }
        .table td, .table th { vertical-align: middle; padding: 0.5rem; }
        .table thead th { white-space: nowrap; }
        .table-wrapper { overflow-x: auto; }
        @media (max-width: 576px) {
            .btn-sm { font-size: 0.75rem; padding: 0.3rem; }
        }
    </style>
</head>
<body>
<div class="container-fluid p-3">
    <h5 class="text-center text-primary mb-3">Шахматка зайнятості тренерів</h5>
    <div class="table-wrapper shadow-sm rounded border">
        <table class="table table-bordered text-center align-middle mb-0">
            <thead class="table-dark">
            <tr>
                <th>Час</th>
                <th>Іван П.</th>
                <th>Олена С.</th>
                <th>Андрій К.</th>
                <th>Марія І.</th>
            </tr>
            </thead>
            <tbody id="schedule-body"></tbody>
        </table>
    </div>
</div>

<script>
    function generateTimeSlots(start = "08:00", end = "22:00") {
        const times = [];
        const startTime = new Date();
        const [sh, sm] = start.split(":");
        const [eh, em] = end.split(":");

        startTime.setHours(+sh, +sm, 0, 0);
        const endTime = new Date();
        endTime.setHours(+eh, +em, 0, 0);

        while (startTime <= endTime) {
            times.push(startTime.toTimeString().slice(0, 5));
            startTime.setMinutes(startTime.getMinutes() + 30);
        }
        return times;
    }

    const schedule = generateTimeSlots();
    const trainers = ["Іван П.", "Олена С.", "Андрій К.", "Марія І."];

    // Приклад розмітки — далі можна буде завантажувати з БД
    const data = {};
    schedule.forEach(time => {
        data[time] = trainers.map(() => Math.random() < 0.5 ? "free" : "busy");
    });

    let reserves = {};

    function formatTimeToDate(timeStr) {
        const [h, m] = timeStr.split(":");
        const d = new Date();
        d.setHours(+h, +m, 0, 0);
        return d;
    }

    function isPast(timeStr) {
        return formatTimeToDate(timeStr) < new Date();
    }

    function renderSchedule() {
        const tbody = document.getElementById("schedule-body");
        tbody.innerHTML = "";
        let nearestMarked = false;

        schedule.forEach(time => {
            const row = document.createElement("tr");
            const th = document.createElement("th");
            th.innerText = time;
            row.appendChild(th);

            const statuses = data[time] || Array(trainers.length).fill("free");

            trainers.forEach((trainer, idx) => {
                const status = statuses[idx];
                const key = `${trainer}|${time}`;
                const td = document.createElement("td");
                const btn = document.createElement("button");

                if (isPast(time)) {
                    btn.className = "btn btn-secondary btn-sm w-100";
                    btn.disabled = true;
                    btn.innerText = "⏳ Минуло";
                } else if (status === "free") {
                    btn.className = "btn btn-success btn-sm w-100";
                    btn.innerText = "✅ Вільно";
                    btn.onclick = () => alert(`✅ Ви записані до ${trainer} на ${time}`);
                    if (!nearestMarked) {
                        btn.classList.add("border", "border-4", "border-primary");
                        nearestMarked = true;
                    }
                } else if (reserves[key]) {
                    btn.className = "btn btn-info btn-sm w-100";
                    btn.disabled = true;
                    btn.innerText = "📌 Ви в резерві";
                } else {
                    btn.className = "btn btn-warning btn-sm w-100";
                    btn.innerText = "🟠 Резерв";
                    btn.onclick = () => {
                        reserves[key] = true;
                        renderSchedule();
                        alert(`🟠 Ви додані до резерву ${trainer} на ${time}`);
                    };
                }

                td.appendChild(btn);
                row.appendChild(td);
            });

            tbody.appendChild(row);
        });
    }

    renderSchedule();
</script>
</body>
</html>
