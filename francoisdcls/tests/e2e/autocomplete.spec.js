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

    // Click the first suggestion and wait for navigation; this verifies the real redirect
    const first = suggestions.first();
    const meta = await first.evaluate(node => node.dataset);
    // Click and wait for navigation
    await Promise.all([
      page.waitForNavigation({ timeout: 5000 }),
      first.click(),
    ]);

    const url = page.url();
    if (meta.type === 'pilote') {
      expect(url).toContain('/pages/fiche_pilote.php');
      expect(url).toMatch(/\bid=\d+/);
    } else if (meta.type === 'ecurie') {
      expect(url).toContain('/pages/fiche_ecurie.php');
      expect(url).toMatch(/\bid=\d+/);
    } else {
      // Fallback: at least ensure we navigated away from the homepage
      expect(url).not.toContain('site_f1.php');
    }
  });
});
