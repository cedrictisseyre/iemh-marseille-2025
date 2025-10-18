const { test, expect } = require('@playwright/test');

// This test assumes the dev server is running at http://127.0.0.1:8000
// and that the homepage is site_f1.php

test.describe('Autocomplete', () => {
  test('shows suggestions and allows keyboard navigation', async ({ page }) => {
    await page.goto('/site_f1.php');
    const input = page.locator('#input-recherche-home');
    await expect(input).toBeVisible();

    await input.fill('Vettel');
    // wait for suggestions to appear
    const suggestions = page.locator('#suggestions-list .suggest-item');
    await expect(suggestions.first()).toBeVisible({ timeout: 3000 });

    // press ArrowDown to select first suggestion and then Enter
    await page.keyboard.press('ArrowDown');
    // The active item should have class active
    const active = page.locator('#suggestions-list .suggest-item.active');
    await expect(active).toHaveCount(1);

    // Ensure pressing Enter triggers navigation (we can't follow navigation to external pages in CI reliably)
    // Instead, check that the dataset contains piloteId or ecurieId
    const data = await active.evaluate(node => node.dataset);
    expect(data.type === 'pilote' || data.type === 'ecurie').toBeTruthy();
  });
});
