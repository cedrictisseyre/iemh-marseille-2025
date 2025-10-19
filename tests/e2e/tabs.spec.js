const { test, expect } = require('@playwright/test');

test.describe('Navigation tabs', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/site_f1.php');
  });

  const selectors = [
    'a[href="pages/liste_pilotes.php"]',
    'a[href="pages/liste_ecuries.php"]',
    'a[href="pages/statistiques.php"]',
    'a[href="pages/recherche.php"]',
    'a[href="pages/comparer_pilotes.php"]',
    'a[href="pages/palmares_annee.php"]',
    'a[href="pages/pantheon_pilotes.php"]'
  ];

  for (const sel of selectors) {
    test(`click ${sel} loads content`, async ({ page }) => {
      await page.click(sel);
      await page.waitForSelector('#main-content');
      const content = await page.locator('#main-content').innerHTML();
      expect(content.length).toBeGreaterThan(80);
    });
  }
});
