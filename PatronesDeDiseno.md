# Patrones de diseño aplicados

Este documento resume los patrones de diseño de interfaz que se usan en la tienda de vinos y champanes, con foco en la coherencia visual, la navegabilidad y la reducción de carga cognitiva.

## 1. Jerarquía Z

La jerarquía Z es un patrón visual que guía la lectura de izquierda a derecha y de arriba hacia abajo, siguiendo el movimiento natural de la vista en una pantalla con composición horizontal.

### Cómo se aplicó en la tienda

- En el encabezado se colocó el logo en una posición de alta visibilidad, seguido de la navegación principal y de accesos persistentes al carrito y al inicio de sesión cuando existen.
- En la zona superior del inicio se usó un bloque introductorio con propuesta de valor y un panel de búsqueda visible, de forma que la atención avance desde la identidad de la marca hacia la acción principal.
- En las páginas de catálogo se priorizó el resumen de la sección, el botón de agregar producto y el acceso al carrito, manteniendo las llamadas a la acción dentro de la misma línea visual.

### Qué problema UX resuelve

- Evita que el usuario tenga que buscar los accesos principales.
- Reduce la fricción al entrar al sitio, porque deja claras las acciones prioritarias.
- Mejora la orientación visual en pantallas grandes y móviles, especialmente en una tienda con varios niveles de exploración.

## 2. Grid + Cards

El patrón Grid + Cards organiza el contenido en una retícula consistente, donde cada tarjeta representa una unidad de información comparable.

### Cómo se utiliza actualmente en el catálogo

- Los productos se presentan en tarjetas reutilizables con imagen, productor, nombre, procedencia, precio y botón de detalle.
- Las categorías también se muestran como cards, manteniendo la misma lógica de exploración entre vistas.
- El layout responsive adapta la cantidad de columnas según el tamaño de pantalla para preservar legibilidad.

### Beneficios para exploración de productos

- Facilita comparar productos de forma rápida.
- Mantiene una estructura visual estable entre categorías, resultados y destacados.
- Permite escanear el catálogo sin saturar al usuario con demasiada información al mismo tiempo.

## 3. Información progresiva

La información progresiva consiste en mostrar primero lo esencial y revelar los detalles adicionales cuando el usuario los necesita.

### Cómo se utiliza en los productos

- En las tarjetas del catálogo se muestra únicamente lo indispensable para decidir si vale la pena profundizar: imagen, nombre, productor, origen y precio.
- En la vista de detalle se despliega la ficha completa con más atributos técnicos como añada, uva, alcohol, volumen, stock y calificación.
- El carrito y el checkout también siguen este principio al resumir primero el total y luego mostrar el desglose cuando es útil para la decisión final.

### Beneficios para reducir carga cognitiva

- Evita abrumar al usuario con demasiados datos desde el inicio.
- Hace que la comparación sea más rápida y menos pesada visualmente.
- Ordena la experiencia de compra por niveles de profundidad, desde exploración hasta confirmación.
