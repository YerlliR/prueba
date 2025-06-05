<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
// Verify user is logged in
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Empresa</title>
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/crearEmpresa.css">
</head>
<body>
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>
    
    <div class="container">
        <h1>Crear Nueva Empresa</h1>
        
        <form id="company-form" action="../actions/crearEmpresa.php" method="POST" enctype="multipart/form-data">
            <div class="logo-upload">
                <div class="logo-upload-icon">
                    <span>+</span>
                </div>
                <div class="logo-upload-text">Subir logo de la empresa</div>
                <div class="logo-upload-hint">Formatos: JPG, PNG (Máx 2MB)</div>
                <input type="file" id="company-logo" name="logo" style="display: none;">
            </div>
            
            <div class="form-group">
                <label for="company-name">Nombre de la empresa *</label>
                <input type="text" id="company-name" name="nombre" class="form-control" placeholder="Ingrese el nombre completo de la empresa" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="company-sector">Sector *</label>
                    <select id="company-sector" name="sector" class="form-control" required>
                        <option value="" selected disabled>Seleccionar sector</option>
                        <option value="Agricola">Agrícola</option>
                        <option value="Alimenticio">Alimenticio</option>
                        <option value="Comercio">Comercio</option>
                        <option value="Educacion">Educación</option>
                        <option value="Energia">Energía</option>
                        <option value="Finanzas">Finanzas</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Inmobiliario">Inmobiliario</option>
                        <option value="Manufactura">Manufactura</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Salud">Salud</option>
                        <option value="Tecnologia">Tecnología</option>
                        <option value="Transporte">Transporte</option>
                        <option value="Turismo">Turismo</option>
                        <option value="Otros">Otros</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="company-employees">Número de empleados</label>
                    <input type="number" id="company-employees" name="numero_empleados" class="form-control" placeholder="Ej: 10" min="1">
                </div>
            </div>
            
            <div class="form-group">
                <label for="company-description">Descripción</label>
                <textarea id="company-description" name="descripcion" class="form-control" rows="4" placeholder="Breve descripción de la empresa y sus actividades"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="company-country">País *</label>
                    <select id="company-country" name="pais" class="form-control" required>
                        <option value="" selected disabled>Seleccionar país</option>
                        <option value="Afganistán">Afganistán</option>
                        <option value="Albania">Albania</option>
                        <option value="Alemania">Alemania</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Antigua y Barbuda">Antigua y Barbuda</option>
                        <option value="Arabia Saudita">Arabia Saudita</option>
                        <option value="Argelia">Argelia</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaiyán">Azerbaiyán</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bangladés">Bangladés</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Bélgica">Bélgica</option>
                        <option value="Belice">Belice</option>
                        <option value="Benín">Benín</option>
                        <option value="Bielorrusia">Bielorrusia</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bosnia y Herzegovina">Bosnia y Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Brasil">Brasil</option>
                        <option value="Brunéi">Brunéi</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cabo Verde">Cabo Verde</option>
                        <option value="Camboya">Camboya</option>
                        <option value="Camerún">Camerún</option>
                        <option value="Canadá">Canadá</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Chipre">Chipre</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoras">Comoras</option>
                        <option value="Corea del Norte">Corea del Norte</option>
                        <option value="Corea del Sur">Corea del Sur</option>
                        <option value="Costa de Marfil">Costa de Marfil</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Croacia">Croacia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Dinamarca">Dinamarca</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egipto">Egipto</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Emiratos Árabes Unidos">Emiratos Árabes Unidos</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Eslovaquia">Eslovaquia</option>
                        <option value="Eslovenia">Eslovenia</option>
                        <option value="España">España</option>
                        <option value="Estados Unidos">Estados Unidos</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Etiopía">Etiopía</option>
                        <option value="Filipinas">Filipinas</option>
                        <option value="Finlandia">Finlandia</option>
                        <option value="Fiyi">Fiyi</option>
                        <option value="Francia">Francia</option>
                        <option value="Gabón">Gabón</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Gibraltar">Gibraltar</option>
                        <option value="Granada">Granada</option>
                        <option value="Grecia">Grecia</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-Bisáu">Guinea-Bisáu</option>
                        <option value="Guinea Ecuatorial">Guinea Ecuatorial</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haití">Haití</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Hungría">Hungría</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Irak">Irak</option>
                        <option value="Irán">Irán</option>
                        <option value="Irlanda">Irlanda</option>
                        <option value="Islandia">Islandia</option>
                        <option value="Islas Caimán">Islas Caimán</option>
                        <option value="Islas Cook">Islas Cook</option>
                        <option value="Islas Feroe">Islas Feroe</option>
                        <option value="Islas Malvinas">Islas Malvinas</option>
                        <option value="Islas Marshall">Islas Marshall</option>
                        <option value="Islas Salomón">Islas Salomón</option>
                        <option value="Israel">Israel</option>
                        <option value="Italia">Italia</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japón">Japón</option>
                        <option value="Jordania">Jordania</option>
                        <option value="Kazajistán">Kazajistán</option>
                        <option value="Kenia">Kenia</option>
                        <option value="Kirguistán">Kirguistán</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="Kosovo">Kosovo</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Lesoto">Lesoto</option>
                        <option value="Letonia">Letonia</option>
                        <option value="Líbano">Líbano</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libia">Libia</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lituania">Lituania</option>
                        <option value="Luxemburgo">Luxemburgo</option>
                        <option value="Macedonia del Norte">Macedonia del Norte</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malasia">Malasia</option>
                        <option value="Malaui">Malaui</option>
                        <option value="Maldivas">Maldivas</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marruecos">Marruecos</option>
                        <option value="Mauricio">Mauricio</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="México">México</option>
                        <option value="Micronesia">Micronesia</option>
                        <option value="Moldavia">Moldavia</option>
                        <option value="Mónaco">Mónaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Níger">Níger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Noruega">Noruega</option>
                        <option value="Nueva Zelanda">Nueva Zelanda</option>
                        <option value="Omán">Omán</option>
                        <option value="Pakistán">Pakistán</option>
                        <option value="Palau">Palau</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="company-city">Ciudad</label>
                    <input type="text" id="company-city" name="ciudad" class="form-control" placeholder="Ej: Madrid">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="company-phone">Teléfono de contacto</label>
                    <input type="tel" id="company-phone" name="telefono" class="form-control" placeholder="+34 XXX XXX XXX">
                </div>
                
                <div class="form-group">
                    <label for="company-email">Email de contacto *</label>
                    <input type="email" id="company-email" name="email" class="form-control" placeholder="empresa@ejemplo.com" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="company-website">Sitio web</label>
                <input type="url" id="company-website" name="sitio_web" class="form-control" placeholder="https://www.empresa.com">
            </div>
            
            <div class="checkbox-container">
                <input type="checkbox" id="company-active" name="estado" checked>
                <label for="company-active">Establecer como empresa activa</label>
            </div>
            
            <div class="checkbox-container">
                <input type="checkbox" id="terms-conditions" required>
                <label for="terms-conditions">Acepto los términos y condiciones</label>
            </div>
            
            <div class="buttons">
                <button type="submit" class="btn btn-primary">Crear Empresa</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='seleccionEmpresa.php'">Cancelar</button>
            </div>
        </form>
    </div>
    
    <script>
        // Script para manejar la carga del logo
        document.querySelector('.logo-upload').addEventListener('click', function() {
            document.getElementById('company-logo').click();
        });
        
        document.getElementById('company-logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileName = file.name;
                document.querySelector('.logo-upload-text').textContent = fileName;
            }
        });
    </script>
        <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>