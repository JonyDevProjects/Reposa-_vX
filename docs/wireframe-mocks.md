# Wireframes de la Aplicación Reposa+

Este documento contiene los esquemas estructurales (wireframes de baja fidelidad) para las pantallas principales de la aplicación Reposa+, utilizando diagramas de bloques con sintaxis de Mermaid.

## 1. Pantalla de Inicio (Home) (`/`)

Vista principal de bienvenida de la tienda con productos destacados y categorías.

```mermaid
block-beta
  columns 20
  
  %% Header
  Logo["Logo (Reposa+)"]:6
  Nav["Home | Catalog"]:8
  Cart["Carrito | Mi Perfil"]:6

  %% Hero Section
  HeroSpace1[" "]:20
  HeroTitle["Your rest, our priority\n\nDiscover the collection..."]:20
  HeroSpace2[" "]:20
  BtnCat["View Catalog"]:10
  BtnSell["Best Sellers"]:10
  HeroSpace3[" "]:20

  %% Categories
  CatTitle["Explore by Category"]:20
  Cat1["Cat 1"]:4
  Cat2["Cat 2"]:4
  Cat3["Cat 3"]:4
  Cat4["Cat 4"]:4
  Cat5["Cat 5"]:4

  %% Featured Products
  FeatTitle["Our Featured Products\n(The best technology...)"]:20
  
  %% Product 1 and 2
  Prod1Img["Image"]:4
  Prod1Body["Valoración\nProducto 1\nPrecio\nDescripción"]:6
  Prod2Img["Image"]:4
  Prod2Body["Valoración\nProducto 2\nPrecio\nDescripción"]:6
  
  %% Product 3 and 4
  Prod3Img["Image"]:4
  Prod3Body["Valoración\nProducto 3\nPrecio\nDescripción"]:6
  Prod4Img["Image"]:4
  Prod4Body["Valoración\nProducto 4\nPrecio\nDescripción"]:6
  
  %% Full store button
  FullStoreBtn["View full store"]:20

  %% Features
  FeatSpace1[" "]:20
  Feat1["Express Shipping"]:6
  Feat2["Rest Guarantee"]:8
  Feat3["Certified Health"]:6
  FeatSpace2[" "]:20

  %% Footer
  Foot["© 2026 Reposa+. All rights reserved."]:20
```

## 2. Pantalla de Catálogo (`/catalog`)

Vista principal para explorar todos los productos, con opciones de filtrado.

```mermaid
block-beta
  columns 20
  
  %% Header
  Logo["Logo (Reposa+)"]:6
  Nav["Home | Catalog"]:8
  Cart["Carrito | Mi Perfil"]:6
  
  Space1[" "]:20
  
  %% Title
  Title["Catálogo de Almohadas"]:20
  
  Space2[" "]:20

  %% Layout: Sidebar (3) + Grid (9)
  %% Sidebar Filters
  FilterBlock["Filtros\n- Categoría\n- Precio\n- Valoración"]:4
  
  %% Product Grid
  Prod1Img["[Img]"]:4 Prod1Body["Producto 1\nPrecio"]:4
  Prod2Img["[Img]"]:4 Prod2Body["Producto 2\nPrecio"]:4

  FilterSpace[" "]:4
  Prod3Img["[Img]"]:4 Prod3Body["Producto 3\nPrecio"]:4
  Prod4Img["[Img]"]:4 Prod4Body["Producto 4\nPrecio"]:4

  
  %% Footer
  Foot["© 2026 Reposa+. All rights reserved."]:20
```

## 3. Detalle del Producto (`/catalog/{product}`)

Vista enfocada en la información detallada de una almohada específica.

```mermaid
block-beta
  columns 20
  
  %% Header
  Logo["Logo (Reposa+)"]:6
  Nav["Home | Catalog"]:8
  Cart["Carrito | Mi Perfil"]:6
  
  %% Breadcrumb
  Breadcrumb["Inicio > Catálogo > Almohada Viscoelástica"]:20
  
  Space1[" "]:20
  
  %% Product Area
  Image["[Imagen Grande del Producto]"]:10
  Info["Título: Almohada Viscoelástica\n\nPrecio: 45.99€\n\nDescripción detallada...\nMateriales, Ergonomía..."]:10
  
  AddBtnSpace[" "]:10
  AddToCartBtn["Añadir al Carrito"]:10
  
  
  %% Footer
  Foot["© 2026 Reposa+. All rights reserved."]:20
```

## 4. Pantalla de Carrito (`/cart`)

Resumen de los artículos añadidos y proceso previo al checkout.

```mermaid
block-beta
  columns 20
  
  %% Header
  Logo["Logo (Reposa+)"]:6
  Nav["Home | Catalog"]:8
  Cart["Carrito | Mi Perfil"]:6
  
  Title["Tu Carrito de Compras"]:20
  
  Space1[" "]:20
  
  %% Items list
  Item1Img["[Img]"]:4 Item1Info["Almohada Visco\nCant: 1"]:8 Item1Price["45.99€"]:4 Item1Del["Eliminar"]:4
  Item2Img["[Img]"]:4 Item2Info["Almohada Gel\nCant: 2"]:8 Item2Price["119.98€"]:4 Item2Del["Eliminar"]:4
  
  Space2[" "]:20
  
  %% Summary
  SummarySpace[" "]:12 SummaryTotal["Total: 165.97€"]:8
  CheckoutSpace[" "]:12 CheckoutBtn["Proceder al Pago"]:8
  
  Space3[" "]:20
  
  %% Footer
  Foot["© 2026 Reposa+. All rights reserved."]:20
```

## 5. Perfil de Usuario (`/profile` / `/orders`)

Panel del usuario donde puede ver su información, direcciones y su historial de pedidos.

```mermaid
block-beta
  columns 20
  
  %% Header
  Logo["Logo (Reposa+)"]:6
  Nav["Home | Catalog"]:8
  Cart["Carrito | Mi Perfil"]:6
  
  Title["Mi Perfil"]:20
  
  Space1[" "]:20
  
  %% Tabs/Menu
  Tab1["Información Personal"]:6
  Tab2["Mis Direcciones"]:8
  Tab3["Mis Pedidos"]:6
  
  Space2[" "]:20
  
  %% Content (Mis Pedidos)
  OrderTitle["Historial de Pedidos"]:20
  Order1["Pedido #1024 - 12/05/2026 - 165.97€ - Entregado"]:16 BtnO1["Ver"]:4
  Order2["Pedido #1025 - 28/05/2026 - 45.99€ - En Camino"]:16 BtnO2["Ver"]:4
  
  Space3[" "]:20
  
  %% Footer
  Foot["© 2026 Reposa+. All rights reserved."]:20
```

## 6. Dashboard de Administración (`/admin/dashboard`)

Panel exclusivo para los administradores para gestionar la tienda.

```mermaid
block-beta
  columns 20
  
  %% Admin Header
  AdminLogo["Admin Panel"]:4 AdminNav["Dashboard | Productos | Categorías | Pedidos"]:12 AdminLogout["Salir"]:4
  
  Space1[" "]:20
  
  %% Stats
  Stat1["Usuarios\n150"]:5 Stat2["Pedidos\n320"]:5 Stat3["Ingresos\n$5,400"]:5 Stat4["Productos\n24"]:5
  
  Space2[" "]:20
  
  %% Recent Orders Table
  TableTitle["Últimos Pedidos Recibidos"]:20
  TableRow1["#1030 - Juan Pérez - 120.00€ - Pendiente"]:16 BtnA1["Gestionar"]:4
  TableRow2["#1029 - María Gómez - 45.99€ - Completado"]:16 BtnA2["Gestionar"]:4
  
  Space3[" "]:20
  
  %% Quick Actions
  ActionTitle["Acciones Rápidas"]:20
  BtnNewProd["+ Nuevo Producto"]:6 BtnNewCat["+ Nueva Categoría"]:8 BtnRep["Generar Reporte"]:6
```
