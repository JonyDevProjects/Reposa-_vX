import { test, expect } from '@playwright/test';

test.describe.configure({ mode: 'serial' });

test.describe('Certificación Fase 5: Casos de Prueba 1 al 5 — Reposa+ E2E', () => {
  let createdOrderId;
  let createdGuestToken;
  let guestEmail;

  test('Caso 1: Registro de usuario con dirección obligatoria y validaciones', async ({ page }) => {
    // 1. Navegar a /register
    await page.goto('/register');
    await expect(page).toHaveTitle(/registro|crear cuenta/i);

    // 2. Validar que al enviar vacío fallan los campos requeridos
    await page.click('button[type="submit"]');

    // Comprobar que existen campos con validación requerida o alerta de error
    await expect(page.locator('.is-invalid, .alert-danger').first()).toBeVisible();

    // 3. Rellenar formulario con dirección de Madrid
    const timestamp = Date.now();
    const testEmail = `carlos.pruebas.${timestamp}@reposatest.es`;

    await page.fill('#name', 'Carlos Pruebas E2E');
    await page.fill('#email', testEmail);
    await page.fill('#password', 'Password123!');
    await page.fill('#password_confirmation', 'Password123!');
    await page.fill('#street', 'Calle Mayor 45, 3º A');
    await page.fill('#city', 'Madrid');
    await page.fill('#zip_code', '28013');
    await page.fill('#province', 'Madrid');
    await page.fill('#phone', '+34 600 123 456');
    await page.check('#terms');

    // 4. Enviar formulario
    await Promise.all([
      page.waitForURL(url => url.pathname.includes('/profile') || url.pathname.includes('/catalog')),
      page.click('button[type="submit"]')
    ]);

    // Si redirigió a /catalog o similar, navegar expresamente a /profile
    if (!page.url().includes('/profile')) {
      await page.goto('/profile');
    }

    // 5. Verificar que el usuario queda registrado con dirección principal en /profile
    await expect(page.locator('body')).toContainText('Carlos Pruebas E2E');
    await expect(page.locator('#addresses')).toContainText('Calle Mayor 45, 3º A');
    await expect(page.locator('#addresses')).toContainText('28013');
    await expect(page.locator('#addresses')).toContainText('Madrid');
    await expect(page.locator('#addresses')).toContainText(/principal|main/i);

    // Verificar teléfono en datos de perfil
    const phoneInput = page.locator('input[name="phone"]');
    await expect(phoneInput).toHaveValue('+34 600 123 456');
  });

  test('Caso 2: Compra completa como invitado (Guest Checkout) con Correos Express', async ({ browser }) => {
    // Usar sesión limpia sin autenticación previa
    const context = await browser.newContext();
    const page = await context.newPage();

    // 1. Navegar a /catalog y añadir producto al carrito
    await page.goto('/catalog');
    const addToCartBtn = page.locator('.btn-cart-add').first();
    await expect(addToCartBtn).toBeVisible();
    await Promise.all([
      page.waitForResponse(resp => resp.url().includes('/cart/add/') && resp.status() === 200),
      addToCartBtn.click()
    ]);

    // 2. Navegar a /checkout
    await page.goto('/checkout');
    await expect(page.locator('body')).toContainText(/invitado|guest/i);

    // 3. Rellenar datos de envío del invitado (Barcelona)
    const timestamp = Date.now();
    guestEmail = `laura.invitada.${timestamp}@reposatest.es`;

    await page.fill('#shipping_name', 'Laura Invitada E2E');
    await page.fill('#shipping_email', guestEmail);
    await page.fill('#shipping_phone', '+34 611 998 877');
    await page.fill('#shipping_street', 'Avenida Diagonal 120, 1º');
    await page.fill('#shipping_city', 'Barcelona');
    await page.fill('#shipping_zip_code', '08018');
    await page.fill('#shipping_province', 'Barcelona');

    // 4. Seleccionar método de envío Correos Express (express_24h)
    const expressRadio = page.locator('input[value="express_24h"]');
    await expressRadio.check();

    // Verificar que el desglose refleja el método express (7.95€)
    await expect(page.locator('#summary-shipping-cost')).toContainText('7.95');

    // 4.5. Seleccionar método de pago directo para prueba de tracking y albarán
    const directRadio = page.locator('input[value="direct"]');
    if (await directRadio.count() > 0) {
      await directRadio.check();
    }

    // 5. Confirmar pedido directo
    await Promise.all([
      page.waitForURL(url => url.pathname.includes('/orders/') && url.searchParams.has('token')),
      page.click('#checkout-form button[type="submit"]')
    ]);

    // 6. Validar redirección a /orders/{id}?token={guest_token}
    const currentUrl = new URL(page.url());
    const match = currentUrl.pathname.match(/\/orders\/(\d+)/);
    expect(match).not.toBeNull();
    createdOrderId = match[1];
    createdGuestToken = currentUrl.searchParams.get('token');

    expect(createdOrderId).toBeTruthy();
    expect(createdGuestToken).toHaveLength(40);

    // Verificar datos en la pantalla de confirmación
    await expect(page.locator('#order-confirmation-heading')).toBeVisible();
    await expect(page.locator('body')).toContainText('Laura Invitada E2E');
    await expect(page.locator('body')).toContainText(createdOrderId);

    // Verificar que aparece el tracking inicial de paquetería
    await expect(page.locator('body')).toContainText(/correos express/i);
    await expect(page.locator('body')).toContainText(/RPX2026/);

    await context.close();
  });

  test('Caso 3: Seguridad de pedidos y facturas (HTTP 403 Forbidden sin token vs 200 OK con token)', async ({ browser }) => {
    expect(createdOrderId).toBeTruthy();
    expect(createdGuestToken).toBeTruthy();

    // Contexto completamente limpio (sin cookies ni estado de sesión)
    const cleanContext = await browser.newContext();
    const cleanPage = await cleanContext.newPage();

    // 1. Acceder a /orders/{id} sin token -> esperar HTTP 403
    const resNoToken = await cleanPage.goto(`/orders/${createdOrderId}`);
    expect(resNoToken.status()).toBe(403);

    // 2. Acceder a /orders/{id} con token falso -> esperar HTTP 403
    const resFakeToken = await cleanPage.goto(`/orders/${createdOrderId}?token=token_falso_invalido_12345678901234567890`);
    expect(resFakeToken.status()).toBe(403);

    // 3. Descargar factura sin token -> esperar HTTP 403
    const resInvoiceNoToken = await cleanPage.goto(`/orders/${createdOrderId}/invoice`);
    expect(resInvoiceNoToken.status()).toBe(403);

    // 4. Descargar factura con token válido -> comprobar descarga y cabeceras PDF
    const resInvoiceValid = await cleanContext.request.get(`/orders/${createdOrderId}/invoice?token=${createdGuestToken}`);
    expect(resInvoiceValid.status()).toBe(200);
    expect(resInvoiceValid.headers()['content-type']).toContain('application/pdf');
    expect(resInvoiceValid.headers()['content-disposition']).toContain('attachment');
    expect(resInvoiceValid.headers()['content-disposition']).toContain('.pdf');

    const pdfBuffer = await resInvoiceValid.body();
    expect(pdfBuffer.toString('utf-8', 0, 4)).toBe('%PDF');
    expect(pdfBuffer.length).toBeGreaterThan(1000);

    await cleanContext.close();
  });

  test('Caso 4: Conversión de invitado en 1 clic (Claim Account) y vinculación al historial', async ({ browser }) => {
    expect(createdOrderId).toBeTruthy();
    expect(createdGuestToken).toBeTruthy();

    const context = await browser.newContext();
    const page = await context.newPage();

    // 1. Acceder a la confirmación con el token válido
    await page.goto(`/orders/${createdOrderId}?token=${createdGuestToken}`);

    // 2. Localizar tarjeta "Guarda tu cuenta en 1 clic"
    const claimForm = page.locator('form[action*="claim-account"]');
    await expect(claimForm).toBeVisible();
    await expect(page.locator('body')).toContainText(guestEmail);

    // 3. Rellenar contraseña
    await claimForm.locator('input[name="password"]').fill('MiSecreto2026!');
    await claimForm.locator('input[name="password_confirmation"]').fill('MiSecreto2026!');

    // 4. Enviar formulario
    await Promise.all([
      page.waitForURL(url => url.pathname.includes('/profile')),
      claimForm.locator('button[type="submit"]').click()
    ]);

    // 5. Verificar que el usuario queda autenticado en /profile
    await expect(page.locator('body')).toContainText('Laura Invitada E2E');

    // 6. Verificar que el pedido aparece vinculado en "Mis Pedidos"
    await expect(page.locator('#orders')).toContainText(`#${createdOrderId}`);

    // 7. Verificar que la dirección de Barcelona se guardó como principal
    await expect(page.locator('#addresses')).toContainText('Avenida Diagonal 120, 1º');
    await expect(page.locator('#addresses')).toContainText('Barcelona');
    await expect(page.locator('#addresses')).toContainText(/principal|main/i);

    // 8. Volver a visitar el pedido: ahora autenticado se visualiza directamente y no se muestra el formulario de claim
    await page.goto(`/orders/${createdOrderId}`);
    expect(page.url()).toContain(`/orders/${createdOrderId}`);
    await expect(page.locator('form[action*="claim-account"]')).toHaveCount(0);

    await context.close();
  });

  test('Caso 5: Operativa de paquetería en panel admin, avance de tracking y albarán térmico A6', async ({ browser }) => {
    expect(createdOrderId).toBeTruthy();

    const adminContext = await browser.newContext();
    const page = await adminContext.newPage();

    // 1. Iniciar sesión como administrador
    await page.goto('/login');
    await page.fill('#email', 'admin@reposaplus.com');
    await page.fill('#password', 'admin123');
    await Promise.all([
      page.waitForURL(url => !url.pathname.includes('/login')),
      page.click('button[type="submit"]')
    ]);

    // 2. Navegar a /admin/orders
    await page.goto('/admin/orders');
    await expect(page.locator('h1, h2, h3').filter({ hasText: /pedidos/i })).toBeVisible();

    // 3. Localizar fila del pedido
    const paddedId = `#${String(createdOrderId).padStart(5, '0')}`;
    const orderRow = page.locator('tr').filter({ hasText: paddedId });
    await expect(orderRow).toBeVisible();

    // Comprobar datos de tracking
    await expect(orderRow).toContainText('RPX2026');
    await expect(orderRow).toContainText(/Etiqueta creada|Pre-admitido/i);

    // 4. Pulsar botón "Avanzar" para avanzar estado del envío
    const advanceBtn = orderRow.locator('button:has-text("Avanzar")');
    await Promise.all([
      page.waitForResponse(resp => resp.url().includes('/admin/shipments/') && resp.status() < 400),
      advanceBtn.click()
    ]);

    // Recargar vista admin para comprobar estado actualizado
    await page.goto('/admin/orders');
    const updatedRow = page.locator('tr').filter({ hasText: paddedId });
    // El estado del envío avanza a "En tránsito" (badge azul) y el pedido a "shipped"
    await expect(updatedRow).toContainText(/tránsito|shipped|enviado/i);

    // 5. Obtener enlace de la etiqueta térmica A6 y abrirlo
    const labelLink = updatedRow.locator('a:has-text("Etiqueta")');
    const labelUrl = await labelLink.getAttribute('href');
    expect(labelUrl).toContain('/admin/shipments/');

    await page.goto(labelUrl);

    // 6. Validar componentes de la etiqueta térmica A6
    await expect(page.locator('.label-container')).toBeVisible();
    await expect(page.locator('.routing-box')).toBeVisible();
    await expect(page.locator('.barcode-stripes')).toBeVisible();
    await expect(page.locator('body')).toContainText('Laura Invitada E2E');
    await expect(page.locator('body')).toContainText(/barcelona/i);
    await expect(page.locator('body')).toContainText(/correos express/i);
    await expect(page.locator('body')).toContainText(/RPX2026/);

    await adminContext.close();
  });

  test('Caso 6: Verificación de iniciación de flujo Google OAuth 2.0 y parámetros de consentimiento', async ({ page }) => {
    await page.goto('/login');
    const googleBtn = page.locator('a[href*="/auth/google"]');
    await expect(googleBtn).toBeVisible();

    // Interceptar la navegación hacia accounts.google.com
    const [request] = await Promise.all([
      page.waitForRequest(req => req.url().startsWith('https://accounts.google.com/o/oauth2/auth')),
      googleBtn.click()
    ]);

    const googleUrl = new URL(request.url());
    expect(googleUrl.searchParams.get('client_id')).toMatch(/(932690824736.*|mock-client-id-ci)\.apps\.googleusercontent\.com$/);
    expect(googleUrl.searchParams.get('redirect_uri')).toMatch(/^http:\/\/localhost(:8000)?\/(api\/)?auth\//);
    expect(googleUrl.searchParams.get('scope')).toBe('openid profile email');
    expect(googleUrl.searchParams.get('response_type')).toBe('code');
    expect(googleUrl.searchParams.get('state')).toBeTruthy();
  });

  test('Caso 6.4: Iniciación de Google OAuth desde el Checkout adaptativo con parámetro redirect=checkout', async ({ browser }) => {
    const context = await browser.newContext();
    const page = await context.newPage();

    // 1. Añadir producto al carrito
    await page.goto('/catalog');
    const addToCartBtn = page.locator('.btn-cart-add').first();
    await expect(addToCartBtn).toBeVisible();
    await Promise.all([
      page.waitForResponse(resp => resp.url().includes('/cart/add/') && resp.status() === 200),
      addToCartBtn.click()
    ]);

    // 2. Navegar a /checkout
    await page.goto('/checkout');
    await expect(page).toHaveURL(/\/checkout/);

    // 3. Localizar el botón de Google en el aviso de compra como invitado
    const checkoutGoogleBtn = page.locator('.card a[href*="/auth/google"]');
    await expect(checkoutGoogleBtn).toBeVisible();
    await expect(checkoutGoogleBtn).toHaveAttribute('href', /redirect=checkout/);

    // 4. Interceptar navegación hacia Google OAuth
    const [request] = await Promise.all([
      page.waitForRequest(req => req.url().startsWith('https://accounts.google.com/o/oauth2/auth')),
      checkoutGoogleBtn.click()
    ]);

    const googleUrl = new URL(request.url());
    expect(googleUrl.searchParams.get('client_id')).toMatch(/(932690824736.*|mock-client-id-ci)\.apps\.googleusercontent\.com$/);
    expect(googleUrl.searchParams.get('redirect_uri')).toMatch(/^http:\/\/localhost(:8000)?\/(api\/)?auth\//);

    await context.close();
  });

  test('Caso 6.5: Verificación de iniciación de pasarela Stripe Checkout desde el formulario de compra', async ({ browser }) => {
    const context = await browser.newContext();
    const page = await context.newPage();

    // 1. Añadir producto al carrito
    await page.goto('/catalog');
    const addToCartBtn = page.locator('.btn-cart-add').first();
    await expect(addToCartBtn).toBeVisible();
    await Promise.all([
      page.waitForResponse(resp => resp.url().includes('/cart/add/') && resp.status() === 200),
      addToCartBtn.click()
    ]);

    // 2. Navegar a /checkout
    await page.goto('/checkout');
    await expect(page).toHaveURL(/\/checkout/);

    // 3. Rellenar datos de envío obligatorios
    await page.fill('#shipping_name', 'Stripe Test User');
    await page.fill('#shipping_email', 'stripe.tester@example.com');
    await page.fill('#shipping_phone', '+34 622 111 333');
    await page.fill('#shipping_street', 'Gran Vía 28');
    await page.fill('#shipping_city', 'Madrid');
    await page.fill('#shipping_zip_code', '28013');

    // 4. Verificar que Stripe es el método de pago seleccionado por defecto
    const stripeRadio = page.locator('input[value="stripe"]');
    await expect(stripeRadio).toBeChecked();

    // 5. Interceptar navegación hacia Stripe Checkout (checkout.stripe.com)
    const [request] = await Promise.all([
      page.waitForRequest(req => req.url().startsWith('https://checkout.stripe.com/')),
      page.click('#checkout-form button[type="submit"]')
    ]);

    expect(request.url()).toMatch(/^https:\/\/checkout\.stripe\.com\//);

    await context.close();
  });
});
