<header class="top-header">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-dark d-md-none me-3" id="sidebarToggle">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h5 class="mb-0 fw-bold text-dark d-none d-md-block">@yield('page_title')</h5>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light position-relative rounded-circle shadow-sm" type="button"
                    id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="far fa-bell"></i>
                    <span id="notificationBadge"
                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                        <span class="visually-hidden">Alertas</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" aria-labelledby="notificationDropdown"
                    style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <h6 class="mb-0 fw-bold">Notificaciones</h6>
                        <span class="badge bg-primary rounded-pill" id="notificationCount">0</span>
                    </div>
                    <div id="notificationList" class="py-2">
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-check-circle fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">Todo al día</p>
                        </div>
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="{{ route('inventario.tallas') }}"
                            class="text-decoration-none small text-primary fw-medium">Ver todo el inventario</a>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const badge = document.getElementById('notificationBadge');
                    const countLabel = document.getElementById('notificationCount');
                    const listContainer = document.getElementById('notificationList');

                    function fetchNotifications() {
                        fetch('{{ route("api.notifications") }}')
                            .then(response => response.json())
                            .then(data => {
                                // Actualizar contador y badge
                                countLabel.textContent = data.count;
                                if (data.count > 0) {
                                    badge.classList.remove('d-none');
                                } else {
                                    badge.classList.add('d-none');
                                }

                                // Actualizar lista
                                if (data.count > 0) {
                                    listContainer.innerHTML = '';
                                    data.notifications.forEach(notif => {
                                        const item = `
                                            <a href="${notif.url}" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 text-wrap">
                                                <div class="rounded-circle ${notif.bg} d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                                    <i class="${notif.icon} ${notif.color}"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="small fw-bold text-dark">${notif.title}</div>
                                                    <div class="small text-muted mb-1">${notif.message}</div>
                                                    <div class="text-xs text-secondary" style="font-size: 0.7rem;">${notif.time}</div>
                                                </div>
                                            </a>
                                        `;
                                        listContainer.insertAdjacentHTML('beforeend', item);
                                    });
                                } else {
                                    listContainer.innerHTML = `
                                        <div class="text-center py-4 text-muted small">
                                            <i class="fas fa-check-circle fa-2x mb-2 opacity-25"></i>
                                            <p class="mb-0">Todo al día</p>
                                        </div>
                                    `;
                                }
                            })
                            .catch(err => console.error('Error fetching notifications:', err));
                    }

                    // Carga inicial y poll cada 30 segundos
                    fetchNotifications();
                    setInterval(fetchNotifications, 30000);
                });
            </script>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                    id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar small bg-primary text-white">{{ substr(auth()->user()->name, 0, 2) }}</div>
                    <span class="d-none d-md-block text-dark small fw-medium">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item small" href="#"><i class="fas fa-user-circle me-2"></i>Perfil</a></li>
                    <li><a class="dropdown-item small" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item small text-danger" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>