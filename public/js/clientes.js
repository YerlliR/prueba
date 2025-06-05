// public/js/clientes.js - VERSIÓN ACTUALIZADA CON ELIMINACIÓN DE CLIENTES

document.addEventListener('DOMContentLoaded', function() {
    // Función para inicializar después de que el sistema de alertas esté disponible
    const initClientes = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initClientes, 100);
            return;
        }
        
        const searchInput = document.querySelector('.search-input');
        const viewButtons = document.querySelectorAll('.btn-view');
        const favoriteButtons = document.querySelectorAll('.btn-favorite');
        const contactButtons = document.querySelectorAll('.btn-contact');
        const removeButtons = document.querySelectorAll('.btn-remove');
        
        // ===== BÚSQUEDA DE EMPRESAS =====
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filtrarEmpresas(searchTerm);
            });
        }
        
        function filtrarEmpresas(searchTerm) {
            // Filtrar en la tabla
            const tableRows = document.querySelectorAll('.empresas-table tbody tr');
            tableRows.forEach(row => {
                const empresaName = row.querySelector('.empresa-name')?.textContent.toLowerCase() || '';
                const empresaDescription = row.querySelector('.empresa-description')?.textContent.toLowerCase() || '';
                const empresaSector = row.querySelector('.tag')?.textContent.toLowerCase() || '';
                
                if (empresaName.includes(searchTerm) || 
                    empresaDescription.includes(searchTerm) ||
                    empresaSector.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Filtrar en las tarjetas
            const empresaCards = document.querySelectorAll('.empresa-card');
            empresaCards.forEach(card => {
                const empresaName = card.querySelector('.empresa-name')?.textContent.toLowerCase() || '';
                const empresaDescription = card.querySelector('.empresa-description')?.textContent.toLowerCase() || '';
                const empresaSector = card.querySelector('.tag')?.textContent.toLowerCase() || '';
                
                if (empresaName.includes(searchTerm) || 
                    empresaDescription.includes(searchTerm) ||
                    empresaSector.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // ===== VER PERFIL =====
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const empresaRow = this.closest('tr');
                const empresaCard = this.closest('.empresa-card');
                const empresaId = this.getAttribute('data-empresa-id');
                
                let empresaData = {};
                
                if (empresaRow) {
                    empresaData = {
                        nombre: empresaRow.querySelector('.empresa-name')?.textContent || '',
                        descripcion: empresaRow.querySelector('.empresa-description')?.textContent || '',
                        sector: empresaRow.querySelector('.tag')?.textContent || '',
                        email: empresaRow.querySelector('.empresa-contact div:first-child')?.textContent || '',
                        telefono: empresaRow.querySelector('.empresa-contact div:last-child')?.textContent || ''
                    };
                } else if (empresaCard) {
                    empresaData = {
                        nombre: empresaCard.querySelector('.empresa-name')?.textContent || '',
                        descripcion: empresaCard.querySelector('.empresa-description')?.textContent || '',
                        sector: empresaCard.querySelector('.tag')?.textContent || '',
                        email: empresaCard.querySelector('.info-value')?.textContent || '',
                        telefono: empresaCard.querySelectorAll('.info-value')[1]?.textContent || ''
                    };
                }
                
                mostrarPerfilEmpresa(empresaData);
            });
        });
        
        function mostrarPerfilEmpresa(empresaData) {
            const modalHtml = `
                <div class="modal active" id="modal-perfil-empresa">
                    <div class="modal-content" style="max-width: 600px;">
                        <div class="modal-header">
                            <h2>${empresaData.nombre}</h2>
                            <button class="modal-close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="perfil-header" style="display: flex; gap: 20px; margin-bottom: 25px;">
                                <div class="perfil-avatar" style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 600;">
                                    ${empresaData.nombre.substring(0, 2).toUpperCase()}
                                </div>
                                <div class="perfil-info">
                                    <h3 style="margin: 0 0 10px 0; color: var(--text-color);">${empresaData.nombre}</h3>
                                    <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                                        <span style="display: flex; align-items: center; gap: 5px; color: var(--text-light);">
                                            <i class="fas fa-tag"></i> ${empresaData.sector}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <div style="display: flex; color: #f59e0b;">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span style="margin-left: 5px; font-weight: 600;">4.5</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <h4 style="color: var(--primary-color); margin-bottom: 10px;">Descripción</h4>
                                <p style="color: var(--text-light); line-height: 1.6;">${empresaData.descripcion || 'Sin descripción disponible'}</p>
                            </div>
                            
                            <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                                <h4 style="color: var(--primary-color); margin-bottom: 15px;">Información de contacto</h4>
                                <div style="display: flex; flex-direction: column; gap: 15px;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <span>${empresaData.email || 'No disponible'}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <span>${empresaData.telefono || 'No disponible'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary modal-close">Cerrar</button>
                            <button class="btn btn-primary" onclick="window.open('mailto:${empresaData.email}', '_blank')">
                                <i class="fas fa-envelope"></i> Contactar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Eliminar modal anterior si existe
            const modalAnterior = document.getElementById('modal-perfil-empresa');
            if (modalAnterior) modalAnterior.remove();
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
            
            const modal = document.getElementById('modal-perfil-empresa');
            modal.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    setTimeout(() => modal.remove(), 300);
                });
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    setTimeout(() => modal.remove(), 300);
                }
            });
        }
        
        // ===== FAVORITOS =====
        favoriteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const icon = this.querySelector('i');
                const empresaRow = this.closest('tr');
                const empresaCard = this.closest('.empresa-card');
                const empresaName = empresaRow ? 
                    empresaRow.querySelector('.empresa-name')?.textContent :
                    empresaCard?.querySelector('.empresa-name')?.textContent;
                
                if (icon.classList.contains('far')) {
                    // Añadir a favoritos
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    
                    showAlert({
                        type: 'success',
                        title: 'Añadido a favoritos',
                        message: `${empresaName} se ha añadido a tus favoritos`,
                        duration: 3000
                    });
                } else {
                    // Quitar de favoritos
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    
                    showAlert({
                        type: 'info',
                        title: 'Eliminado de favoritos',
                        message: `${empresaName} se ha eliminado de tus favoritos`,
                        duration: 3000
                    });
                }
            });
        });
        
        // ===== CONTACTAR =====
        contactButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const empresaRow = this.closest('tr');
                const empresaCard = this.closest('.empresa-card');
                const empresaName = empresaRow ? 
                    empresaRow.querySelector('.empresa-name')?.textContent :
                    empresaCard?.querySelector('.empresa-name')?.textContent;
                const empresaEmail = empresaRow ?
                    empresaRow.querySelector('.empresa-contact div:first-child')?.textContent :
                    empresaCard?.querySelector('.info-value')?.textContent;
                
                showAlert({
                    type: 'info',
                    title: 'Contactar empresa',
                    message: `Abriendo cliente de correo para contactar con ${empresaName}`,
                    duration: 3000
                });
                
                // Abrir cliente de correo
                setTimeout(() => {
                    window.open(`mailto:${empresaEmail}?subject=Consulta comercial&body=Hola, me gustaría obtener más información sobre sus servicios.`, '_blank');
                }, 1000);
            });
        });
        
        // ===== ELIMINAR RELACIÓN CON CLIENTE =====
        removeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const relacionId = this.getAttribute('data-relacion-id');
                const empresaName = this.getAttribute('data-empresa-nombre');
                const empresaRow = this.closest('tr');
                const empresaCard = this.closest('.empresa-card');
                
                if (!relacionId || !empresaName) {
                    showAlert({
                        type: 'error',
                        title: 'Error',
                        message: 'No se pudo obtener la información necesaria para eliminar la relación'
                    });
                    return;
                }
                
                // Mostrar confirmación personalizada
                mostrarConfirmacionEliminar(
                    `¿Estás seguro de que deseas terminar la relación comercial con "${empresaName}"?`,
                    'Esta acción eliminará la relación y ya no aparecerá en tu lista de clientes. Esta acción no se puede deshacer.',
                    () => eliminarRelacionCliente(relacionId, empresaRow || empresaCard, empresaName)
                );
            });
        });
        
        function mostrarConfirmacionEliminar(titulo, mensaje, callback) {
            const modalHtml = `
                <div class="modal active" id="modal-confirmar-eliminar-cliente" style="z-index: 10001;">
                    <div class="modal-content" style="max-width: 450px;">
                        <div class="modal-header">
                            <h2 style="color: #dc2626; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Confirmar eliminación
                            </h2>
                        </div>
                        <div class="modal-body">
                            <p style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-color);">${titulo}</p>
                            <p style="color: var(--text-light); line-height: 1.5;">${mensaje}</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="btn-cancelar-eliminar-cliente">Cancelar</button>
                            <button class="btn" id="btn-confirmar-eliminar-cliente" style="background: #dc2626; color: white;">
                                <i class="fas fa-trash"></i> Terminar Relación
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Eliminar modal anterior si existe  
            const modalAnterior = document.getElementById('modal-confirmar-eliminar-cliente');
            if (modalAnterior) modalAnterior.remove();
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
            
            const modal = document.getElementById('modal-confirmar-eliminar-cliente');
            const btnCancelar = document.getElementById('btn-cancelar-eliminar-cliente');
            const btnConfirmar = document.getElementById('btn-confirmar-eliminar-cliente');
            
            const cerrarModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
                setTimeout(() => modal.remove(), 300);
            };
            
            btnCancelar.addEventListener('click', cerrarModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) cerrarModal();
            });
            
            btnConfirmar.addEventListener('click', () => {
                cerrarModal();
                callback();
            });
        }
        
        function eliminarRelacionCliente(relacionId, element, empresaName) {
            const loadingId = showAlert({
                type: 'loading',
                title: 'Terminando relación...',
                message: `Eliminando relación comercial con ${empresaName}`,
                persistent: true
            });
            
            const formData = new FormData();
            formData.append('relacionId', relacionId);
            
            fetch('../../php/actions/terminarRelacion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideAlert(loadingId);
                
                if (data.success) {
                    showAlert({
                        type: 'success',
                        title: 'Relación terminada',
                        message: data.message || `La relación comercial con ${empresaName} ha sido terminada correctamente`,
                        duration: 5000
                    });
                    
                    // Eliminar el elemento del DOM con animación
                    if (element) {
                        element.style.transition = 'all 0.5s ease';
                        element.style.opacity = '0';
                        element.style.transform = 'translateX(-20px)';
                        
                        setTimeout(() => {
                            element.remove();
                            verificarEstadoVacioClientes();
                        }, 500);
                    }
                } else {
                    showAlert({
                        type: 'error',
                        title: 'Error al terminar relación',
                        message: data.message || 'No se pudo terminar la relación comercial. Inténtalo de nuevo.'
                    });
                }
            })
            .catch(error => {
                hideAlert(loadingId);
                console.error('Error:', error);
                showAlert({
                    type: 'error',
                    title: 'Error de conexión',
                    message: 'No se pudo conectar con el servidor. Verifica tu conexión e inténtalo de nuevo.'
                });
            });
        }
        
        function verificarEstadoVacioClientes() {
            const filasVisibles = document.querySelectorAll('.empresas-table tbody tr:not([style*="display: none"])');
            const tarjetasVisibles = document.querySelectorAll('.empresa-card:not([style*="display: none"])');
            
            // Verificar si solo queda la fila de "no hay datos" o no hay filas
            const filasConDatos = Array.from(filasVisibles).filter(fila => 
                !fila.textContent.includes('No hay clientes vinculados')
            );
            
            if (filasConDatos.length === 0 && tarjetasVisibles.length === 0) {
                const contenedorTabla = document.querySelector('.empresas-table tbody');
                const contenedorTarjetas = document.querySelector('.empresas-cards');
                
                const mensajeVacio = `
                    <div class="empty-state" style="padding: 40px; text-align: center; width: 100%;">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 20px; color: #cbd5e1;"></i>
                        <p>No hay clientes vinculados actualmente.</p>
                        <p>Las empresas que te contraten como proveedor aparecerán aquí.</p>
                    </div>
                `;
                
                if (contenedorTabla && !contenedorTabla.querySelector('.empty-state')) {
                    contenedorTabla.innerHTML = `<tr><td colspan="4">${mensajeVacio}</td></tr>`;
                }
                
                if (contenedorTarjetas && !contenedorTarjetas.querySelector('.empty-state')) {
                    contenedorTarjetas.innerHTML = mensajeVacio;
                }
            }
        }
        
        // ===== FILTROS ADICIONALES =====
        const filterSelects = document.querySelectorAll('.filter-select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                aplicarFiltros();
            });
        });
        
        function aplicarFiltros() {
            const sectorFilter = document.getElementById('filter-sector')?.value.toLowerCase() || '';
            const searchTerm = searchInput?.value.toLowerCase() || '';
            
            // Aplicar a tabla
            const tableRows = document.querySelectorAll('.empresas-table tbody tr');
            tableRows.forEach(row => {
                // Evitar filtrar la fila de "no hay datos"
                if (row.textContent.includes('No hay clientes vinculados')) {
                    return;
                }
                
                const empresaName = row.querySelector('.empresa-name')?.textContent.toLowerCase() || '';
                const empresaSector = row.querySelector('.tag')?.textContent.toLowerCase() || '';
                
                const cumpleBusqueda = !searchTerm || empresaName.includes(searchTerm);
                const cumpleSector = !sectorFilter || empresaSector === sectorFilter;
                
                row.style.display = (cumpleBusqueda && cumpleSector) ? '' : 'none';
            });
            
            // Aplicar a tarjetas
            const empresaCards = document.querySelectorAll('.empresa-card');
            empresaCards.forEach(card => {
                const empresaName = card.querySelector('.empresa-name')?.textContent.toLowerCase() || '';
                const empresaSector = card.querySelector('.tag')?.textContent.toLowerCase() || '';
                
                const cumpleBusqueda = !searchTerm || empresaName.includes(searchTerm);
                const cumpleSector = !sectorFilter || empresaSector === sectorFilter;
                
                card.style.display = (cumpleBusqueda && cumpleSector) ? '' : 'none';
            });
        }
        
        console.log('Funcionalidad de clientes inicializada correctamente');
    };
    
    // Inicializar
    initClientes();
});