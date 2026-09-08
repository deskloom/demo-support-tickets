// Smoke test against a running `php artisan serve` instance (default http://127.0.0.1:8123).
// Verifies the ticket lifecycle end to end: create via API -> filter reflects it -> web detail
// page renders it -> validation errors return proper status codes.
//
// Usage:
//   php artisan serve --port=8123 &
//   node smoke-test.mjs
const base = process.env.DEMO_BASE_URL || 'http://127.0.0.1:8123';

function assert(cond, message) {
  if (!cond) throw new Error('FAILED: ' + message);
  console.log('ok: ' + message);
}

async function main() {
  const before = await (await fetch(base + '/api/tickets')).json();

  const res = await fetch(base + '/api/tickets', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({
      category_id: 1,
      title: 'スモークテスト起票',
      description: 'smoke-test.mjsからの自動投稿',
      priority: 'high',
    }),
  });
  assert(res.status === 201, `POST /api/tickets returns 201 (got ${res.status})`);
  const created = await res.json();
  assert(created.data.status === 'open', 'new ticket defaults to open status');

  const after = await (await fetch(base + '/api/tickets')).json();
  assert(after.data.length === before.data.length + 1, 'ticket count increased by 1');

  const filtered = await (await fetch(base + '/api/tickets?status=resolved')).json();
  assert(filtered.data.every((t) => t.status === 'resolved'), 'status filter only returns matching tickets');

  const detail = await fetch(base + `/tickets/${created.data.id}`);
  assert(detail.status === 200, 'web detail page renders the new ticket');
  const detailHtml = await detail.text();
  assert(detailHtml.includes('スモークテスト起票'), 'detail page contains the new ticket title');

  const badPriority = await fetch(base + '/api/tickets', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ category_id: 1, title: 'x', description: 'y', priority: 'urgent' }),
  });
  assert(badPriority.status === 422, 'invalid priority is rejected with 422');

  const missingTitle = await fetch(base + '/api/tickets', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ category_id: 1, description: 'y', priority: 'low' }),
  });
  assert(missingTitle.status === 422, 'missing title is rejected with 422');

  const notFound = await fetch(base + '/tickets/999999');
  assert(notFound.status === 404, 'unknown ticket id returns 404');

  console.log('\nAll checks passed.');
}

main().catch((e) => {
  console.error(e.message);
  process.exit(1);
});
