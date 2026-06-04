import { expect, test } from '@playwright/test'

test('logs in and opens dashboard', async ({ page }) => {
  await page.route((url) => url.pathname.endsWith('/auth/login'), async (route) => {
    await route.fulfill({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: {
          access_token: 'token-e2e',
          token_type: 'Bearer',
          expires_at: null,
          user: {
            id: 'user-1',
            tenant_id: 'tenant-1',
            name: 'Admin Demo',
            email: 'admin@autoestoque.test',
            role: 'admin',
          },
        },
      }),
    })
  })

  await page.route((url) => url.pathname.endsWith('/dashboard'), async (route) => {
    await route.fulfill({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: {
          tenant_id: 'tenant-1',
          date: '2026-06-04',
          total_products: 12,
          products_below_minimum: 2,
          products_zero_stock: 1,
          total_stock_value_in_cents: 150000,
          today_movements: 4,
          recent_movements: [],
        },
      }),
    })
  })

  await page.route((url) => url.pathname.endsWith('/dashboard/most-consumed-products'), async (route) => {
    await route.fulfill({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: [],
        meta: {
          tenant_id: 'tenant-1',
          period_from: null,
          period_to: null,
          total: 0,
        },
      }),
    })
  })

  await page.goto('/login')
  await page.waitForLoadState('networkidle')
  await page.waitForFunction(() => Boolean((document.querySelector('#__nuxt') as HTMLElement & { __vue_app__?: unknown } | null)?.__vue_app__))

  await page.getByPlaceholder('owner@autoestoque.test').fill('admin@autoestoque.test')
  await page.getByPlaceholder('password').fill('secret123')
  const loginResponsePromise = page.waitForResponse((response) => response.url().includes('/auth/login'))

  await page.getByRole('button', { name: 'Entrar' }).click()

  const loginResponse = await loginResponsePromise

  expect(loginResponse.ok()).toBe(true)

  await expect(page).toHaveURL(/\/dashboard$/)
  await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible()
  await expect(page.getByText('12')).toBeVisible()
})
