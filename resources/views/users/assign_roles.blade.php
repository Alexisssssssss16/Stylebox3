@extends('layouts.admin')

@section('title', 'Cambiar Rol — ' . $user->name)

@push('styles')
    <style>
        .role-card {
            border: 2px solid #e9ecef;
            border-radius: 14px;
            padding: 1.1rem 1.4rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        .role-card:hover {
            border-color: #adb5bd;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
            transform: translateY(-1px);
        }

        .role-card.selected {
            border-color: var(--accent-color, #d4a017);
            background: #fffbf0;
            box-shadow: 0 4px 18px rgba(212, 160, 23, 0.15);
        }

        .role-card input[type="radio"] {
            display: none;
        }

        .role-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .role-check {
            width: 22px;
            height: 22px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .role-card.selected .role-check {
            background: var(--accent-color, #d4a017);
            border-color: var(--accent-color, #d4a017);
            color: #fff;
        }

        .access-panel {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            transition: all 0.3s ease;
        }

        .access-panel .module-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 50px;
            padding: 4px 12px;
            font-size: 0.78rem;
            color: #495057;
            margin: 3px;
        }

        .admin-guard {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-size: 0.85rem;
            color: #856404;
        }

        .user-avatar-lg {
            width: 42px;
            height: 42px;
            background: #1a1a1a;
            color: #d4a017;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold mb-1"><i class="fas fa-user-tag me-2 text-warning"></i>Cambiar Rol</h4>
                    <p class="text-muted small mb-0">Selecciona un rol para definir el nivel de acceso del usuario</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>

            {{-- User Info --}}
            <div class="card card-custom mb-4">
                <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
                    <div class="user-avatar-lg">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    <div>
                        <div class="fw-bold">{{ $user->name }}</div>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </div>
                    <div class="ms-auto">
                        @php $currentRole = $user->roles->first(); @endphp
                        @if($currentRole)
                            @php $curCfg = $roleConfig[$currentRole->name] ?? null; @endphp
                            <span class="badge {{ $curCfg['badge_class'] ?? 'bg-secondary text-white' }}">
                                <i class="{{ $curCfg['icon'] ?? 'fas fa-user-tag' }} me-1"></i>
                                {{ $curCfg['label'] ?? ucfirst($currentRole->name) }}
                            </span>
                        @else
                            <span class="badge bg-secondary text-white">Sin rol</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Admin Guard Warning --}}
            @if($user->hasRole('admin') && \App\Models\User::role('admin')->count() <= 1)
                <div class="admin-guard mb-4">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Este usuario es el único administrador del sistema.</strong>
                    No podrás cambiarle el rol hasta que haya al menos otro administrador.
                </div>
            @endif

            <div class="row g-4">

                {{-- LEFT: Role Selector --}}
                <div class="col-lg-7">
                    <form action="{{ route('admin.users.roles.update', $user) }}" method="POST" id="rolesForm">
                        @csrf

                        <div class="card card-custom mb-4">
                            <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                                <h6 class="fw-bold text-uppercase small text-muted mb-0">
                                    <i class="fas fa-shield-alt me-2 text-warning"></i>Roles disponibles
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex flex-column gap-3">

                                    @foreach($roles as $role)
                                        @php
                                            $cfg = $roleConfig[$role->name] ?? [
                                                'label' => ucfirst($role->name),
                                                'icon' => 'fas fa-user-tag',
                                                'color_bg' => '#6c757d',
                                                'color_text' => '#fff',
                                                'badge_class' => 'bg-secondary text-white',
                                                'description' => 'Rol personalizado.',
                                                'modules' => $role->permissions->pluck('name')->toArray(),
                                            ];
                                            $checked = $user->hasRole($role->name);
                                        @endphp

                                        <label
                                            class="role-card d-flex align-items-center gap-3 {{ $checked ? 'selected' : '' }}"
                                            for="role_{{ $role->id }}" data-role="{{ $role->name }}"
                                            data-modules="{{ json_encode($cfg['modules'] ?? []) }}"
                                            data-desc="{{ $cfg['description'] }}" data-label="{{ $cfg['label'] }}"
                                            data-icon="{{ $cfg['icon'] }}">

                                            <input type="radio" id="role_{{ $role->id }}" name="role" value="{{ $role->name }}"
                                                {{ $checked ? 'checked' : '' }}>

                                            {{-- Role Icon --}}
                                            <div class="role-icon"
                                                style="background:{{ $cfg['color_bg'] }}; color:{{ $cfg['color_text'] }};">
                                                <i class="{{ $cfg['icon'] }}"></i>
                                            </div>

                                            {{-- Role Info --}}
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="fw-bold">{{ $cfg['label'] }}</span>
                                                    @if($role->name === 'admin')
                                                        <span class="badge bg-warning text-dark"
                                                            style="font-size:0.68rem;">Sistema</span>
                                                    @endif
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary ms-auto"
                                                        style="font-size:0.68rem;">
                                                        {{ $role->permissions->count() }} permisos
                                                    </span>
                                                </div>
                                                <div class="text-muted small">{{ $cfg['description'] }}</div>
                                            </div>

                                            {{-- Radio Indicator --}}
                                            <div class="role-check">
                                                <i class="fas fa-check" style="font-size:0.7rem;"></i>
                                            </div>
                                        </label>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary-custom px-5">
                                <i class="fas fa-save me-2"></i>Guardar Rol
                            </button>
                        </div>
                    </form>
                </div>

                {{-- RIGHT: Access Preview Panel --}}
                <div class="col-lg-5">
                    <div class="access-panel sticky-top" style="top: 80px;" id="accessPanel">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div id="previewIcon" class="role-icon"
                                style="background:#1a1a1a; color:#d4a017; width:36px; height:36px; font-size:1rem;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-uppercase text-muted"
                                    style="font-size:0.7rem; letter-spacing:.05em;">
                                    Acceso que obtendrá
                                </div>
                                <div class="fw-bold" id="previewRoleLabel">Selecciona un rol</div>
                            </div>
                        </div>

                        <p class="text-muted small mb-3" id="previewDesc">
                            Al seleccionar un rol, aquí verás exactamente qué módulos puede usar el usuario.
                        </p>

                        <div id="previewModules" class="d-flex flex-wrap">
                            <span class="text-muted small fst-italic">— Ningún rol seleccionado —</span>
                        </div>

                        <hr class="my-3">
                        <p class="small text-muted mb-0">
                            <i class="fas fa-info-circle me-1 text-warning"></i>
                            El rol define todos los permisos de acceso. Solo modifica los permisos individuales si necesitas
                            personalizar.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Link to full edit --}}
            <div class="text-center mt-3">
                <small class="text-muted">
                    ¿Quieres cambiar nombre, email o contraseña?
                    <a href="{{ route('admin.users.edit', $user) }}">Editar perfil completo</a>
                </small>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.role-card');
            const panelLabel = document.getElementById('previewRoleLabel');
            const panelDesc = document.getElementById('previewDesc');
            const panelMods = document.getElementById('previewModules');
            const panelIcon = document.getElementById('previewIcon').querySelector('i');
            const panelIconWrap = document.getElementById('previewIcon');

            /** Icons mapping (bg color keyed by role data-role) */
            const roleColors = {
                admin: { bg: '#1a1a1a', text: '#d4a017' },
                vendedor: { bg: '#198754', text: '#ffffff' },
                comprador: { bg: '#0d6efd', text: '#ffffff' },
            };

            function updatePanel(card) {
                const roleName = card.dataset.role;
                const modules = JSON.parse(card.dataset.modules || '[]');
                const desc = card.dataset.desc || '';
                const label = card.dataset.label || roleName;
                const icon = card.dataset.icon || 'fas fa-user-tag';
                const colors = roleColors[roleName] || { bg: '#6c757d', text: '#fff' };

                panelLabel.textContent = label;
                panelDesc.textContent = desc;
                panelIconWrap.style.background = colors.bg;
                panelIconWrap.style.color = colors.text;
                panelIcon.className = icon;

                if (modules.length === 0) {
                    panelMods.innerHTML = '<span class="text-muted small fst-italic">Sin módulos definidos.</span>';
                } else {
                    panelMods.innerHTML = modules.map(m =>
                        `<span class="module-tag"><i class="fas fa-check-circle" style="color:#198754; font-size:.65rem;"></i>${m}</span>`
                    ).join('');
                }
            }

            // Init panel with currently selected
            const selectedCard = document.querySelector('.role-card.selected');
            if (selectedCard) updatePanel(selectedCard);

            // Click handler
            cards.forEach(function (card) {
                card.addEventListener('click', function () {
                    const radio = this.querySelector('input[type="radio"]');

                    // Update visual selection
                    cards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    // Manually check
                    radio.checked = true;

                    // Update preview panel
                    updatePanel(this);
                });
            });
        });
    </script>
@endpush