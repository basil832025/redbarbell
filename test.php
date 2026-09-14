<?php

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Шахматка тренера</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-free { background-color: #198754; color: white; }
        .btn-reserve { background-color: #ffc107; color: black; }
        .btn-past { background-color: #adb5bd; color: white; }
        td, th { text-align: center; vertical-align: middle; }
    </style>
</head>
<body class="p-3">
<div class="container">
    <h4 class="mb-3">Шахматка зайнятості тренера: <strong>Іваненко Іван</strong></h4>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>Час</th>
                <!-- Дати на 7 днів від сьогодні -->
                <script>
                    const today = new Date();
                    const days = ['нд', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];
                    for (let i = 0; i < 7; i++) {
                        const d = new Date(today);
                        d.setDate(today.getDate() + i);
                        const date = d.getDate().toString().padStart(2, '0');
                        const month = d.toLocaleString('uk-UA', { month: 'short' });
                        document.write(`<th>${days[d.getDay()]}, ${date} ${month}.</th>`);
                    }
                </script>
            </tr>
            </thead>
            <tbody>
            <script>
                function pad(num) {
                    return num.toString().padStart(2, '0');
                }

                function renderButton(status) {
                    switch (status) {
                        case 'free':
                            return '<button class="btn btn-sm w-100 btn-free">Вільно</button>';
                        case 'reserve':
                            return '<button class="btn btn-sm w-100 btn-reserve">Резерв</button>';
                        case 'past':
                            return '<button class="btn btn-sm w-100 btn-past">Минуло</button>';
                    }
                }

                const now = new Date();
                for (let h = 8; h < 22; h++) {
                    for (let m = 0; m < 60; m += 30) {
                        let row = `<tr><td>${pad(h)}:${pad(m)}</td>`;
                        for (let d = 0; d < 7; d++) {
                            const slot = new Date(now);
                            slot.setDate(now.getDate() + d);
                            slot.setHours(h, m, 0, 0);

                            let status;
                            if (d === 0 && slot < now) {
                                status = 'past';
                            } else {
                                // Чергування Вільно / Резерв
                                status = ((h + m + d) % 2 === 0) ? 'free' : 'reserve';
                            }

                            row += `<td>${renderButton(status)}</td>`;
                        }
                        row += '</tr>';
                        document.write(row);
                    }
                }
            </script>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
