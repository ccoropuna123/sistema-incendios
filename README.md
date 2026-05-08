# Sistema Incendios 🔥

Proyecto Laravel para registrar y gestionar reportes de incendios.

---

## 🚀 Instalación

### 1. Clonar el proyecto
```bash
git clone https://github.com/AnthonyConti2203/sistema-incendios.git
cd sistema-incendios
````

### 2. Instalar dependencias

```bash
composer install
```

### 3. Crear archivo .env

```bash
cp .env.example .env
```
### Si estás en Windows y falla el comando, copiar manualmente:
```bash
.env.example → .env
```

### 4. Generar clave

```bash
php artisan key:generate
```

### 5. Crear manualmente la base de datos SQLite

```bash
database/database.sqlite
```
### Luego verificar que en .env exista:
```bash
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 6. Ejecutar migraciones

```bash
php artisan migrate
```

### 7. Iniciar servidor
```bash
php artisan serve
```
### Abrir en navegador:
```bash
http://127.0.0.1:8000
```

---

## 👥 Trabajo en equipo

* Todo se trabaja en la rama `main`

### Antes de empezar:

```bash
git pull origin main
```

### Después de cambios:

```bash
git add .
git commit -m "descripción del cambio"
git push origin main
```

---

## ⚠️ Importante

* ❌ NO subir `.env`
* ❌ NO subir `database/database.sqlite`
* ❌ NO subir `vendor`
