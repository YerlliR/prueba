                     
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RemoteOrder - Gestión de Pedidos Simplificada</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Formas flotantes decorativas -->
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>
    <div class="floating-shape shape3"></div>

    <!-- Botón volver arriba -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Navegación -->
    <nav class="navbar">
        <a href="#" class="logo">
            <i class="fas fa-cube"></i>
            RemoteOrder
        </a>
        
        <ul class="nav-links">
            <li><a href="#features">Características</a></li>
            <!-- <li><a href="#how-it-works">Cómo Funciona</a></li> -->
            <li><a href="#pricing">Precios</a></li>
            <li><a href="#testimonials">Testimonios</a></li>
            <li><a href="#contact">Contacto</a></li>
            <li><a href="php/view/login.php" class="cta-btn">Prueba Gratis</a></li>
        </ul>
        
        <div class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Simplifica la gestión de <span>pedidos y proveedores</span> de tu empresa</h1>
            <p>RemoteOrder es la plataforma integral que conecta a empresas con sus proveedores, optimiza la gestión de pedidos y simplifica la organización de productos en un solo lugar.</p>
            <div class="hero-buttons">
                <a href="php/view/login.php" class="hero-btn btn-primary">
                    <i class="fas fa-rocket"></i>
                    Comenzar Ahora
                </a>
                <!-- <a href="#how-it-works" class="hero-btn btn-secondary">
                    <i class="fas fa-play"></i>
                    Ver Demo
                </a> -->
            </div>
        </div>
        
        <div class="hero-image" style="margin-right: 2rem;">
            <img src="imgWeb/Dashbord.png" alt="Dashboard de RemoteOrder">
        </div>
        
        <div class="wave-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>
    
    <!-- Clients Section -->
    <section class="clients">
        <h2>Empresas que confían en nosotros</h2>
        <div class="clients-grid">
            <img src="imgWeb/g400-logo.png" alt="Logo Cliente 1" class="client-logo">
            <img src="imgWeb/Mercadona.svg.png" alt="Logo Cliente 2" class="client-logo">
            <img src="imgWeb/Fanta_2023.svg" alt="Logo Cliente 6" class="client-logo">
            <img src="imgWeb/Claude_AI_logo.svg" alt="Logo Cliente 4" class="client-logo">
            <img src="imgWeb/logo-grefusa-2020-2x.png" alt="Logo Cliente 5" class="client-logo">
            <img src="imgWeb/logo_footer.2c4cf8d6.svg" alt="Logo Cliente 3" class="client-logo">

        </div>
    </section>
    
    <!-- Statistics Section -->
    <section class="statistics" id="statistics">
        <h2>Optimizando negocios en números</h2>
        <div class="statistics-grid">
            <div class="stat-card">
                <div class="counter-number" data-target="5000">0</div>
                <p class="stat-label">Empresas utilizando RemoteOrder</p>
            </div>
            <div class="stat-card">
                <div class="counter-number" data-target="85">0</div>
                <p class="stat-label">Reducción de errores en pedidos (%)</p>
            </div>
            <div class="stat-card">
                <div class="counter-number" data-target="42">0</div>
                <p class="stat-label">Aumento de eficiencia (%)</p>
            </div>
            <div class="stat-card">
                <div class="counter-number" data-target="72000">0</div>
                <p class="stat-label">Pedidos procesados</p>
            </div>
        </div>
        
        <div class="wave-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Características Principales</h2>
            <p>RemoteOrder ofrece herramientas poderosas para facilitar la gestión de toda tu cadena de suministro en un solo lugar.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Gestión de Proveedores</h3>
                <p>Conecta con proveedores, establece relaciones comerciales y mantén toda la información organizada en un perfil detallado con historial completo.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Pedidos Simplificados</h3>
                <p>Crea, envía y gestiona pedidos con facilidad. Seguimiento en tiempo real del estado de cada orden y notificaciones automáticas en cada etapa.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3>Catálogo de Productos</h3>
                <p>Organiza tu inventario por categorías personalizables. Añade imágenes, detalles técnicos y precios con facilidad para mantener un catálogo actualizado.</p>
            </div>
            
            <!-- <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Análisis y Reportes</h3>
                <p>Visualiza el rendimiento de tu negocio con paneles intuitivos. Genera informes detallados sobre ventas, pedidos y relaciones con proveedores.</p>
            </div> -->
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3>Facturación Integrada</h3>
                <p>Genera facturas automáticamente a partir de tus pedidos. Seguimiento de pagos y recordatorios para mantener tus cuentas al día.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Acceso Multiplataforma</h3>
                <p>Accede a tu cuenta desde cualquier dispositivo. Interfaz adaptativa que funciona perfectamente en escritorio, tabletas y móviles.</p>
            </div>
        </div>
    </section>
    
    <!-- How it Works Section -->
    <!-- <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <h2>Cómo Funciona</h2>
            <p>Descubre lo sencillo que es implementar y utilizar RemoteOrder en tu negocio con estos simples pasos.</p>
        </div>
        
        <div class="how-it-works-steps">
            <div class="how-it-works-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Configuración Inicial</h3>
                    <p>Crea tu cuenta y configura tu perfil de empresa. Personaliza categorías, campos personalizados y ajustes específicos para tu industria.</p>
                    <p>Nuestro equipo de soporte está disponible para ayudarte en todo el proceso de configuración inicial.</p>
                </div>
                <div class="step-image">
                    <img src="public/images/steps/step1.png" alt="Configuración Inicial">
                </div>
            </div>
            
            <div class="how-it-works-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Creación de Catálogo</h3>
                    <p>Carga tus productos o servicios, organizándolos en categorías. Añade descripciones detalladas, imágenes y precios.</p>
                    <p>Importa fácilmente tus productos existentes desde Excel o tu sistema actual mediante nuestra herramienta de importación masiva.</p>
                </div>
                <div class="step-image">
                    <img src="public/images/steps/step2.png" alt="Creación de Catálogo">
                </div>
            </div>
            
            <div class="how-it-works-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Conexión con Proveedores</h3>
                    <p>Invita a tus proveedores actuales o descubre nuevos en nuestro marketplace. Establece relaciones comerciales con un simple proceso de solicitud.</p>
                    <p>Centraliza toda la comunicación y documentación con tus proveedores en una única plataforma.</p>
                </div>
                <div class="step-image">
                    <img src="public/images/steps/step3.png" alt="Conexión con Proveedores">
                </div>
            </div>
            
            <div class="how-it-works-step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Gestión de Pedidos</h3>
                    <p>Crea pedidos fácilmente seleccionando productos de tu catálogo. Establece cantidades, fechas de entrega y condiciones especiales.</p>
                    <p>Realiza un seguimiento en tiempo real del estado de tus pedidos, desde la creación hasta la entrega final.</p>
                </div>
                <div class="step-image">
                    <img src="public/images/steps/step4.png" alt="Gestión de Pedidos">
                </div>
            </div>
        </div>
    </section> -->

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="section-header">
            <h2>Planes y Precios</h2>
            <p>Elige el plan que mejor se adapte a las necesidades de tu empresa. Todos incluyen actualizaciones gratuitas y soporte técnico.</p>
        </div>
        
        <div class="pricing-grid">
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3>Básico</h3>
                    <div class="price">
                        €29<span>/mes</span>
                    </div>
                    <p>Ideal para pequeñas empresas</p>
                </div>
                <div class="pricing-body">
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Hasta 3 usuarios</li>
                        <li><i class="fas fa-check"></i> 100 productos en catálogo</li>
                        <li><i class="fas fa-check"></i> 20 proveedores</li>
                        <li><i class="fas fa-check"></i> Gestión básica de pedidos</li>
                        <li><i class="fas fa-check"></i> Facturación simple</li>
                        <li><i class="fas fa-check"></i> Soporte por email</li>
                    </ul>
                    <a href="php/view/login.php" class="btn-primary">Elegir Plan</a>
                </div>
            </div>
            
            <div class="pricing-card popular">
                <div class="popular-badge">Más Popular</div>
                <div class="pricing-header">
                    <h3>Profesional</h3>
                    <div class="price">
                        €79<span>/mes</span>
                    </div>
                    <p>Para empresas en crecimiento</p>
                </div>
                <div class="pricing-body">
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Hasta 10 usuarios</li>
                        <li><i class="fas fa-check"></i> 500 productos en catálogo</li>
                        <li><i class="fas fa-check"></i> 50 proveedores</li>
                        <li><i class="fas fa-check"></i> Gestión avanzada de pedidos</li>
                        <li><i class="fas fa-check"></i> Facturación completa</li>
                        <li><i class="fas fa-check"></i> Panel de análisis</li>
                        <li><i class="fas fa-check"></i> Soporte prioritario</li>
                    </ul>
                    <a href="php/view/login.php" class="btn-primary">Elegir Plan</a>
                </div>
            </div>
            
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3>Empresarial</h3>
                    <div class="price">
                        €149<span>/mes</span>
                    </div>
                    <p>Para operaciones a gran escala</p>
                </div>
                <div class="pricing-body">
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Usuarios ilimitados</li>
                        <li><i class="fas fa-check"></i> Productos ilimitados</li>
                        <li><i class="fas fa-check"></i> Proveedores ilimitados</li>
                        <li><i class="fas fa-check"></i> API para integración</li>
                        <li><i class="fas fa-check"></i> Automatizaciones personalizadas</li>
                        <li><i class="fas fa-check"></i> Informes avanzados</li>
                        <li><i class="fas fa-check"></i> Soporte 24/7</li>
                        <li><i class="fas fa-check"></i> Configuración personalizada</li>
                    </ul>
                    <a href="php/view/login.php" class="btn-primary">Contactar Ventas</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="section-header">
            <h2>Lo que dicen nuestros clientes</h2>
            <p>Empresas de todos los tamaños confían en RemoteOrder para optimizar sus procesos de gestión de pedidos y proveedores.</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-content">
                    "RemoteOrder ha transformado la manera en que gestionamos nuestra cadena de suministro. Ahora tenemos toda la información centralizada y actualizada en tiempo real. Ha mejorado nuestra eficiencia en un 40%."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">AM</div>
                    <div>
                        <div class="author-name">Ana Martínez</div>
                        <div class="author-title">Directora de Operaciones, TecnoSoluciones</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-content">
                    "La plataforma es intuitiva y fácil de usar. Pudimos empezar a trabajar inmediatamente y nuestros proveedores se adaptaron rápidamente. El soporte técnico es excepcional, siempre disponible cuando lo necesitamos."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">JR</div>
                    <div>
                        <div class="author-name">Javier Rodríguez</div>
                        <div class="author-title">CEO, Distribuciones Europa</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-content">
                    "Desde que implementamos RemoteOrder, hemos reducido los errores en pedidos en un 85%. La automatización de procesos nos ha permitido reasignar recursos a tareas de mayor valor, mejorando nuestra rentabilidad."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">CL</div>
                    <div>
                        <div class="author-name">Carmen López</div>
                        <div class="author-title">Gerente de Compras, Manufacturas del Sur</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="section-header">
            <h2>Contacto</h2>
            <p>Si tienes alguna pregunta o sugerencia, no dudes en ponerte en contacto con nosotros</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-info">
                <h3>Información de Contacto</h3>
                <p>Estamos aquí para ayudarte con cualquier duda que tengas sobre RemoteOrder.</p>
                
                <div class="contact-method">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Dirección</h4>
                        <p>Carrer Jaume I, 6, 46200 Paiporta, Valencia</p>
                    </div>
                </div>
                
                <div class="contact-method">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Teléfono</h4>
                        <p>+34 612 345 678</p>
                    </div>
                </div>
                
                <div class="contact-method">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <p>contacto@remoteorder.com</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h3>Envíanos un mensaje</h3>
                <form action="php/actions/enviar_contacto.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Asunto</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Mensaje</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="form-submit">Enviar mensaje</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <div class="footer-logo">
                    <i class="fas fa-cube"></i>
                    RemoteOrder
                </div>
                <p class="footer-description">
                    Plataforma integral para la gestión de pedidos y proveedores que optimiza los procesos empresariales y mejora la eficiencia.
                </p>
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="footer-heading">Enlaces rápidos</h4>
                <ul class="footer-links">
                    <li><a href="#home">Inicio</a></li>
                    <li><a href="#features">Características</a></li>
                    <li><a href="#how-it-works">Cómo funciona</a></li>
                    <li><a href="#pricing">Precios</a></li>
                    <li><a href="#testimonials">Testimonios</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="footer-heading">Soporte</h4>
                <ul class="footer-links">
                    <li><a href="#">Centro de ayuda</a></li>
                    <li><a href="#">Documentación</a></li>
                    <li><a href="#">Tutoriales</a></li>
                    <li><a href="#">Estado del sistema</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="footer-heading">Legal</h4>
                <ul class="footer-links">
                    <li><a href="#">Términos de servicio</a></li>
                    <li><a href="#">Política de privacidad</a></li>
                    <li><a href="#">Cookies</a></li>
                    <li><a href="#">GDPR</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                &copy; 2025 RemoteOrder. Todos los derechos reservados.
            </div>
            <div class="footer-bottom-links">
                <a href="#">Términos</a>
                <a href="#">Privacidad</a>
                <a href="#">Cookies</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="index.js"></script>
</body>
</html>