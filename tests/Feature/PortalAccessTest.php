<?php

use App\Support\PortalAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'portal.admin_email' => 'admin@test.local',
        'portal.admin_password' => 'admin-secret',
        'portal.owner_email' => 'owner@test.local',
        'portal.owner_password' => 'owner-secret',
    ]);
});

function asAdmin()
{
    return test()->withSession(['admin_logged_in' => true, 'portal_role' => PortalAuth::ROLE_ADMIN]);
}

function asOwner()
{
    return test()->withSession(['admin_logged_in' => true, 'portal_role' => PortalAuth::ROLE_OWNER]);
}

$ownerOnlyGets = [
    '/owner/erm-dashboard',
    '/owner/dashboard',
    '/owner/finance',
    '/owner/finance/print',
    '/owner/reports',
    '/owner/reports/monthly-clinic',
    '/owner/reports/revenue',
    '/owner/reports/inventory',
    '/owner/reports/therapist-performance',
];

$ownerOnlyPosts = [
    '/owner/finance/close',
    '/owner/finance/transactions',
    '/owner/finance/transactions/1/delete',
];

it('redirects guests from every owner and admin page to login', function () use ($ownerOnlyGets) {
    foreach (array_merge($ownerOnlyGets, ['/admin/bookings', '/admin/billings', '/admin/bookings/1/update']) as $url) {
        $this->get($url)->assertRedirect('/admin/login');
    }
});

it('blocks admin from all 4 owner areas (GET)', function () use ($ownerOnlyGets) {
    foreach ($ownerOnlyGets as $url) {
        asAdmin()->get($url)->assertForbidden();
    }
});

it('blocks admin from owner write actions (POST)', function () use ($ownerOnlyPosts) {
    foreach ($ownerOnlyPosts as $url) {
        asAdmin()->post($url)->assertForbidden();
    }
});

it('sends legacy owner URLs to the new owner URLs, which stay forbidden for admin', function () {
    asOwner()->get('/admin/owner-finance?month=2026-10')->assertRedirect('/owner/finance?month=2026-10');
    asOwner()->get('/admin/dashboard')->assertRedirect('/owner/erm-dashboard');
    asOwner()->get('/admin/reports/revenue?x=1')->assertRedirect('/owner/reports/revenue?x=1');
    asOwner()->get('/admin/owner-dashboard')->assertRedirect('/owner/dashboard');

    asAdmin()->followingRedirects()->get('/admin/owner-finance')->assertForbidden();
});

it('lets owner open the owner pages', function () use ($ownerOnlyGets) {
    foreach ($ownerOnlyGets as $url) {
        asOwner()->get($url)->assertSuccessful();
    }
});

it('lets owner drill down into admin pages and admin open its own pages', function () {
    asOwner()->get('/admin/bookings')->assertSuccessful();
    asAdmin()->get('/admin/bookings')->assertSuccessful();
    asAdmin()->get('/admin/billings')->assertSuccessful();
});

it('hides owner menu from admin sidebar and shows it to owner', function () {
    asAdmin()->get('/admin/bookings')
        ->assertDontSee('Owner Finance / Cashflow')
        ->assertDontSee('Reporting Center')
        ->assertDontSee('Dashboard ERM')
        ->assertSee('Logout Admin');

    asOwner()->get('/admin/bookings')
        ->assertSee('Owner Finance / Cashflow')
        ->assertSee('Reporting Center')
        ->assertSee('Logout Owner');
});

it('logs in with the right role and lands on that role home', function () {
    $this->post('/admin/login', ['email' => 'admin@test.local', 'password' => 'admin-secret'])
        ->assertRedirect(PortalAuth::ADMIN_HOME);
    expect(session('portal_role'))->toBe('admin');

    $this->post('/admin/logout');

    $this->post('/admin/login', ['email' => 'owner@test.local', 'password' => 'owner-secret'])
        ->assertRedirect(PortalAuth::OWNER_HOME);
    expect(session('portal_role'))->toBe('owner');
});

it('rejects wrong credentials and a disabled owner account', function () {
    $this->post('/admin/login', ['email' => 'admin@test.local', 'password' => 'nope'])
        ->assertSessionHasErrors('email');

    config(['portal.owner_email' => null, 'portal.owner_password' => null]);
    expect(PortalAuth::attempt('', ''))->toBeNull();
    expect(PortalAuth::attempt('owner@test.local', 'owner-secret'))->toBeNull();
});

it('gives admin the least privilege when credentials collide', function () {
    config(['portal.owner_email' => 'admin@test.local', 'portal.owner_password' => 'admin-secret']);

    expect(PortalAuth::attempt('admin@test.local', 'admin-secret'))->toBe('admin');
});

it('keeps legacy sessions without a role as admin and clears the role on logout', function () {
    $this->withSession(['admin_logged_in' => true])->get('/owner/finance')->assertForbidden();

    asOwner()->post('/admin/logout');
    $this->get('/owner/finance')->assertRedirect('/admin/login');
});
