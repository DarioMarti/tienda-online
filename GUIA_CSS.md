# 📘 Guía de Estilos CSS - Tienda Online

## 🎯 Estructura de Archivos CSS

### ✅ Archivos que DEBES modificar:

#### 1. `styles/input.css` 
**Este es tu archivo principal de estilos personalizados**

```css
@tailwind base;      /* Estilos base de Tailwind */
@tailwind components; /* Componentes de Tailwind */
@tailwind utilities;  /* Utilidades de Tailwind */

/* Aquí van TODOS tus estilos personalizados */
```

**¿Qué puedes hacer aquí?**
- ✅ Añadir estilos personalizados
- ✅ Crear clases CSS personalizadas
- ✅ Definir animaciones (@keyframes)
- ✅ Sobrescribir estilos de Tailwind

**Ejemplo de cómo añadir nuevos estilos:**
```css
/* Añade esto al final de input.css */

/* Botón personalizado */
.btn-primary {
    background: linear-gradient(135deg, #D4AF37, #C5A028);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
}
```

#### 2. `tailwind.config.js`
**Configuración de Tailwind (colores, fuentes, breakpoints, etc.)**

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        // Añade más colores aquí
        'fashion-black': '#111111',
        'fashion-accent': '#D4AF37',
        'fashion-gray': '#F5F5F5',
        // Ejemplo: añadir un nuevo color
        'fashion-rose': '#E8B4B8',
      },
    },
  },
}
```

### ❌ Archivos que NO debes modificar directamente:

#### `styles/output.css`
**Este archivo se genera automáticamente**

- ❌ No lo modifiques manualmente
- ❌ Tus cambios se perderán al recompilar
- ✅ Se regenera automáticamente desde `input.css`

---

## 🔄 Flujo de Trabajo

### Modo Desarrollo (Recomendado)

1. **Inicia el modo watch:**
   ```bash
   npm run dev
   ```

2. **Edita `styles/input.css`** con tus estilos personalizados

3. **Guarda el archivo** → Tailwind recompila automáticamente

4. **Recarga tu navegador** para ver los cambios

### Modo Producción

Cuando termines de desarrollar:
```bash
npm run build
```
Esto genera un CSS optimizado y minificado.

---

## 🎨 Cómo Añadir Estilos Personalizados

### Opción 1: Clases CSS en `input.css`

```css
/* En styles/input.css */

/* Tarjeta de producto */
.product-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
```

Luego úsalo en tu HTML:
```html
<div class="product-card">
    <h3>Producto</h3>
</div>
```

### Opción 2: Extender colores en `tailwind.config.js`

```javascript
// En tailwind.config.js
theme: {
  extend: {
    colors: {
      'primary': '#D4AF37',
      'secondary': '#111111',
      'success': '#10B981',
      'danger': '#EF4444',
    },
  },
}
```

Luego usa las clases de Tailwind:
```html
<button class="bg-primary text-white px-4 py-2 rounded">
    Comprar
</button>
```

### Opción 3: Componentes personalizados con `@layer`

```css
/* En styles/input.css */

@layer components {
  .btn {
    @apply px-4 py-2 rounded font-medium transition-all;
  }
  
  .btn-gold {
    @apply bg-fashion-accent text-white hover:bg-opacity-90;
  }
  
  .btn-outline {
    @apply border-2 border-fashion-black text-fashion-black hover:bg-fashion-black hover:text-white;
  }
}
```

Uso:
```html
<button class="btn btn-gold">Añadir al carrito</button>
<button class="btn btn-outline">Ver más</button>
```

---

## 🎯 Estilos Actuales Disponibles

### Clases Personalizadas Ya Creadas:

#### Colores
- `bg-fashion-black` / `text-fashion-black` → #111111
- `bg-fashion-accent` / `text-fashion-accent` → #D4AF37 (dorado)
- `bg-fashion-gray` / `text-fashion-gray` → #F5F5F5

#### Fuentes
- `font-editorial` → Bodoni Moda (para títulos elegantes)
- `font-sans` → Jost (para texto general)

#### Componentes
- `.editorial-font` → Aplica Bodoni Moda
- `.scrolled` → Estilo del header al hacer scroll
- `.sidebar-link` → Enlaces del sidebar con animación
- `.custom-checkbox` → Checkbox personalizado dorado
- `.marquee-container` / `.marquee-content` → Texto que corre

---

## 💡 Ejemplos Prácticos

### Ejemplo 1: Crear un botón de compra

```css
/* En styles/input.css */
.btn-comprar {
    background: linear-gradient(135deg, #D4AF37, #C5A028);
    color: white;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}

.btn-comprar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.5);
}
```

### Ejemplo 2: Card de producto con hover

```css
/* En styles/input.css */
.product-card {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    transition: all 0.4s ease;
}

.product-card img {
    transition: transform 0.4s ease;
}

.product-card:hover img {
    transform: scale(1.05);
}

.product-card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    padding: 20px;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.product-card:hover .product-card-overlay {
    transform: translateY(0);
}
```

---

## 🚀 Comandos Útiles

```bash
# Modo desarrollo (auto-recompila)
npm run dev

# Compilar para producción (minificado)
npm run build

# Ver qué clases de Tailwind estás usando
npx tailwindcss -i ./styles/input.css -o ./styles/output.css --watch
```

---

## ⚠️ Importante

1. **Siempre edita `input.css`, nunca `output.css`**
2. **Ejecuta `npm run dev` mientras desarrollas**
3. **Guarda los cambios y recarga el navegador**
4. **Para producción, ejecuta `npm run build`**

---

## 📚 Recursos

- [Documentación de Tailwind CSS](https://tailwindcss.com/docs)
- [Tailwind CSS Cheat Sheet](https://nerdcave.com/tailwind-cheat-sheet)
- [Generador de Colores Tailwind](https://uicolors.app/create)
