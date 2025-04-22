<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floor Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .zoom-pan-wrapper {
            width: 100%;
            height: 80vh; /* Вместо фиксированных 600px */
            overflow: auto;
            border: 2px solid #ccc;
            position: relative;
            touch-action: pinch-zoom;
        }

        .desk-map-container {
            transform-origin: 0 0;
            position: absolute;
            top: 0;
            left: 0;
        }

        .desk {
            position: absolute;
            height: 52px;
            color: white;
            font-weight: bold;
            text-align: center;
            line-height: 52px;
            cursor: pointer;
            z-index: 1;
        }

        .desk::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0); /* Прозрачный фон */
            transition: background 0.3s ease;
            z-index: -1;
        }

        .desk:hover::before {
            background: rgba(255, 255, 255, 0.5); /* Побледнение фона */
        }

        .desk.available { background: green; }
        .desk.occupied { background: red; }
        .desk.maintenance { background: gray; }
        .desk.user-booked { background: blue; } 

        /* Всплывающее меню */
        .desk-menu {
            display: none;
            position: absolute;
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 10px;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
            text-align: center;
            pointer-events: auto; /* Сохраняет взаимодействие */
            filter: none; /* Убирает эффект наследования */
        }

        .desk-name {
            position: absolute;
            width: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap; /* Запрещает перенос строки */
            line-height: 1; /* Убирает дополнительный отступ сверху */
        }


        .desk-menu button {
            white-space: nowrap;  /* Запрещает перенос текста */
        }


        .desk.active .desk-menu {
            display: block;
        }

        .desk {
            position: absolute;
            z-index: 1;
        }

        .desk.active {
            z-index: 100; /* Выше всех столов */
        }

        /* Добавляем черный всплывающий блок с номером стола */
        .desk-tooltip {
            display: none;
            position: absolute;
            background: black;
            color: white;
            padding: 2px 4px;
            padding-bottom: 10px;
            border-radius: 5px;
            font-size: 13px;
            white-space: nowrap;
            top: -30px; /* Отступ вверх */
            left: 50%;
            transform: translateX(-50%);
            height: 50px;
        }

        .desk-tooltip,
        .desk.active .desk-tooltip {
            display: block;
        }

        /* Убираем бледность у меню */
        .desk:hover .desk-menu {
            filter: none !important;
        }

        /* Настройка расположения столов в залах */
        .hall-1 { grid-column: 1 / span 5; grid-row: 3 / span 2; }
        .hall-2 { grid-column: 5 / span 2; grid-row: 3 / span 1; }
        .hall-3 { grid-column: 8 / span 4; grid-row: 4 / span 3; }
        .hall-4 { grid-column: 1 / span 6; grid-row: 1 / span 2; }

    </style>
</head>
<body>
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="fw-bold mb-3" style="font-size: 28px;">Floor Map</h1>

    <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">Back to List</a>
    <a href="{{ route('memberships.index') }}" class="btn btn-success mb-3">Show my memberships</a>

    <div class="zoom-pan-wrapper" id="zoom-wrapper">
        <div class="desk-map-container" id="desk-map-container">
            <div id="desk-canvas" style="width: {{ ($maxX + 10) * 10 }}px; height: {{ ($maxY + 10) * 10 }}px;">
                @foreach($desks as $desk)
                    @php
                        $scale = ceil(($desk->capacity ?? 2) / 2);
                        $unitSize = 52;
                        $deskWidth = $unitSize * $scale;
                        $left = ($desk->coordinates_x ?? 0) * 10 - ($deskWidth / 2);
                        $top = ($desk->coordinates_y ?? 0) * 10;
                        $statusClass = $desk->user_booked ? 'user-booked' : $desk->status;
                    @endphp
                    <div class="desk {{ $statusClass }}"
                         data-id="{{ $desk->id }}"
                         data-name="{{ $desk->name }}"
                         data-capacity="{{ $desk->capacity ?? 2 }}"
                         data-status="{{ $desk->status }}"
                         style="width: {{ $deskWidth }}px; left: {{ $left }}px; top: {{ $top }}px;">
                        {{ preg_replace('/[^0-9]/', '', $desk->name) }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal (для админа) -->
<div id="edit-desk-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <form id="edit-desk-form" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Edit Desk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-desk-id">
                <input type="hidden" id="edit-coordinates-x">
                <input type="hidden" id="edit-coordinates-y">

                <label for="edit-desk-name">Name:</label>
                <input type="text" id="edit-desk-name" class="form-control">

                <label for="edit-desk-status">Status:</label>
                <select id="edit-desk-status" class="form-select">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                <label for="edit-desk-location">Location:</label>
                <input type="text" id="edit-desk-location" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" id="delete-desk-btn" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>


<!-- Форма бронирования -->
<div id="reservationModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Desk booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reservationForm" action="{{ route('memberships.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="desk_id" name="desk_id">
                    <label for="membership_type">Membership type:</label>
                    <select name="membership_type" id="membership_type" class="form-select">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <label for="start_date">Start date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                    <label for="end_date">End date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                    <button type="submit" class="btn btn-primary mt-3">Book now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const isAdmin = @json(auth()->user()->role === 'admin');
    const wrapper = document.getElementById('zoom-wrapper');
    const mapContainer = document.getElementById('desk-map-container');

    let scale = 1, panX = 0, panY = 0, isPanning = false, startX = 0, startY = 0, isDraggingDesk = false;

    function updateTransform() {
        mapContainer.style.transform = `translate(${panX}px, ${panY}px) scale(${scale})`;
    }

    // Зум колесом мыши
    wrapper.addEventListener('wheel', e => {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.1 : -0.1;
        scale = Math.max(0.3, Math.min(3, scale + delta));
        updateTransform();
    });

    // Перетаскивание карты
    wrapper.addEventListener('mousedown', e => {
        if (!isDraggingDesk) {
            isPanning = true;
            startX = e.clientX - panX;
            startY = e.clientY - panY;
        }
    });

    wrapper.addEventListener('mousemove', e => {
        if (!isPanning) return;
        panX = e.clientX - startX;
        panY = e.clientY - startY;
        updateTransform();
    });

    wrapper.addEventListener('mouseup', () => isPanning = false);
    wrapper.addEventListener('mouseleave', () => isPanning = false);

    // Действия при клике на стол — для всех
    document.querySelectorAll('.desk').forEach(desk => {
        desk.addEventListener('click', () => {
            const id = desk.dataset.id;
            const name = desk.dataset.name;
            const status = desk.dataset.status;
            const left = parseFloat(desk.style.left);
            const top = parseFloat(desk.style.top);
            const width = parseFloat(desk.style.width);
            const coordX = Math.round((left + width / 2) / 10);
            const coordY = Math.round(top / 10);

            if (isAdmin) {
                document.getElementById('edit-desk-id').value = id;
                document.getElementById('edit-desk-name').value = name;
                document.getElementById('edit-desk-status').value = status;
                document.getElementById('edit-coordinates-x').value = coordX;
                document.getElementById('edit-coordinates-y').value = coordY;

                new bootstrap.Modal(document.getElementById('edit-desk-modal')).show();
            } else {
                document.getElementById('desk_id').value = id;
                new bootstrap.Modal(document.getElementById('reservationModal')).show();
            }
        });
    });

    // Только для админа: перемещение столов
    if (isAdmin) {
        interact('.desk').draggable({
            listeners: {
                start(event) {
                    isDraggingDesk = true;
                    wrapper.style.pointerEvents = 'none';
                    const t = event.target;
                    t.dataset.originalLeft = t.style.left;
                    t.dataset.originalTop = t.style.top;
                },
                move(event) {
                    const t = event.target;
                    const dx = event.dx / scale;
                    const dy = event.dy / scale;
                    t.style.left = `${parseFloat(t.style.left || 0) + dx}px`;
                    t.style.top = `${parseFloat(t.style.top || 0) + dy}px`;
                },
                end(event) {
                    isDraggingDesk = false;
                    wrapper.style.pointerEvents = 'auto';

                    const t = event.target;
                    const id = t.dataset.id;
                    const left = parseFloat(t.style.left);
                    const top = parseFloat(t.style.top);
                    const width = parseFloat(t.style.width);
                    const coordX = Math.round((left + width / 2) / 10);
                    const coordY = Math.round(top / 10);

                    if (confirm("Save new desk position?")) {
                        fetch(`/desks/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                coordinates_x: coordX,
                                coordinates_y: coordY
                            })
                        }).then(res => res.json()).then(res => {
                            if (!res.success) alert("Failed to update.");
                        });
                    } else {
                        t.style.left = t.dataset.originalLeft;
                        t.style.top = t.dataset.originalTop;
                    }
                }
            }
        });

        // Сохранение изменений
        document.getElementById('edit-desk-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit-desk-id').value;

            fetch(`/desks/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: document.getElementById('edit-desk-name').value,
                    status: document.getElementById('edit-desk-status').value,
                    location: document.getElementById('edit-desk-location').value,
                    coordinates_x: document.getElementById('edit-coordinates-x').value,
                    coordinates_y: document.getElementById('edit-coordinates-y').value
                })
            }).then(res => res.json())
              .then(res => {
                if (res.success) location.reload();
                else alert("Failed to update.");
            });
        });

        // Удаление
        document.getElementById('delete-desk-btn').addEventListener('click', function() {
            const id = document.getElementById('edit-desk-id').value;
            if (confirm("Are you sure to delete this desk?")) {
                fetch(`/desks/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                  .then(res => {
                    if (res.success) location.reload();
                    else alert("Failed to delete.");
                });
            }
        });
    }
});
</script>




@endsection
</body>
</html>