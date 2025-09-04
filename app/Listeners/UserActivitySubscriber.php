<?php

namespace App\Listeners;

use App\Events\Product\AddedStockEvent;
use App\Events\Product\BulkDeletedEvent as BulkDeletedProductsEvent;
use App\Events\Product\BulkUpdatedEvent as BulkUpdatedProductsEvent;
use App\Events\Product\CreatedEvent as CreatedProductEvent;
use App\Events\Product\DeletedEvent as DeletedProductEvent;
use App\Events\Product\TookProductEvent;
use App\Events\Product\UpdatedEvent as UpdatedProductEvent;
use App\Events\User\BulkDeletedEvent as BulkDeletedUsersEvent;
use App\Events\User\BulkUpdatedEvent as BulkUpdatedUsersEvent;
use App\Events\User\CreatedEvent as CreatedUserEvent;
use App\Events\User\DeletedEvent as DeletedUserEvent;
use App\Events\User\UpdatedEvent as UpdatedUserEvent;
use App\Models\InventoryProducts;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class UserActivitySubscriber
{
    public function subscribe(Dispatcher $events): array
    {
        return [
            // Product activity
            AddedStockEvent::class => 'handleAddedStock',
            BulkDeletedProductsEvent::class => 'handleBulkDeletedProducts',
            BulkUpdatedProductsEvent::class => 'handleBulkUpdatedProducts',
            CreatedProductEvent::class => 'handleCreatedProduct',
            DeletedProductEvent::class => 'handleDeletedProduct',
            TookProductEvent::class => 'handleTookProduct',
            UpdatedProductEvent::class => 'handleUpdatedProduct',
            // User activity
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
            CreatedUserEvent::class => 'handleCreatedUser',
            DeletedUserEvent::class => 'handleDeletedUser',
            UpdatedUserEvent::class => 'handleUpdatedUser',
            BulkDeletedUsersEvent::class => 'handleBulkDeletedUsers',
            BulkUpdatedUsersEvent::class => 'handleBulkUpdatedUsers',
        ];
    }


    public function handleUserLogin(Login $event): void
    {
        Activity::create([
            'user_id' => $event->user->id,
            'activity_type' => 'logget ind',
            'activity_label' => 'Bruger logget ind',
            'activity_data' => (object) [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'subject_id' => $event->user->id,
            'subject_type' => User::class,
        ]);
    }

    public function handleUserLogout(Logout $event): void
    {
        Activity::create([
            'user_id' => $event->user->id,
            'activity_type' => 'logget ud',
            'activity_label' => 'Bruger logget ud',
            'activity_data' => (object) [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'subject_id' => $event->user->id,
            'subject_type' => User::class,
        ]);
    }

    public function handleAddedStock(AddedStockEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'tilføjet lager',
            'activity_label' => "Tilføjet {$event->quantity} stk. til {$event->product->name}",
            'activity_data' => (object) [
                'product_id' => $event->product->id,
                'quantity' => $event->quantity,
            ],
            'subject_id' => $event->product->id,
            'subject_type' => InventoryProducts::class,
        ]);
    }

    public function handleBulkDeletedProducts(BulkDeletedProductsEvent $event): void
    {
        foreach ($event->products as $product) {
            Activity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Masse sletning af produkter',
                'activity_label' => "Slettet produkt {$product->name}",
                'activity_data' => (object) [
                    'product_id' => $product->id,
                ],
                'subject_id' => $product->id,
                'subject_type' => InventoryProducts::class,
            ]);
        }
    }

    public function handleBulkUpdatedProducts(BulkUpdatedProductsEvent $event): void
    {
        foreach ($event->products as $product) {
            Activity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Masse opdatering af produkter',
                'activity_label' => "Opdateret produkt {$product->name}",
                'activity_data' => (object) [
                    'product_id' => $product->id,
                    'quantity' => $event->quantity,
                    'alert_threshold' => $event->alertThreshold,
                    'should_alert' => $event->shouldAlert,
                ],
                'subject_id' => $product->id,
                'subject_type' => InventoryProducts::class,
            ]);
        }
    }

    public function handleCreatedProduct(CreatedProductEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'oprettet produkt',
            'activity_label' => "Oprettet produkt {$event->product->name}",
            'activity_data' => (object) [
                'product_id' => $event->product->id,
                'quantity' => $event->product->qty,
                'alert_threshold' => $event->product->alert_threshold,
                'should_alert' => $event->product->should_alert,
            ],
            'subject_id' => $event->product->id,
            'subject_type' => InventoryProducts::class,
        ]);
    }

    public function handleDeletedProduct(DeletedProductEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'slettet produkt',
            'activity_label' => "Slettet produkt {$event->product->name}",
            'activity_data' => (object) [
                'product_id' => $event->product->id,
            ],
            'subject_id' => $event->product->id,
            'subject_type' => InventoryProducts::class,
        ]);
    }

    public function handleTookProduct(TookProductEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'taget produkt',
            'activity_label' => "Taget {$event->quantity} stk. af produkt {$event->product->name}",
            'activity_data' => (object) [
                'product_id' => $event->product->id,
                'quantity' => $event->quantity,
            ],
            'subject_id' => $event->product->id,
            'subject_type' => InventoryProducts::class,
        ]);
    }

    public function handleUpdatedProduct(UpdatedProductEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'opdateret produkt',
            'activity_label' => "Opdateret produkt {$event->product->name}",
            'activity_data' => (object) [
                'product_id' => $event->product->id,
                'quantity' => $event->product->qty,
                'alert_threshold' => $event->product->alert_threshold,
                'should_alert' => $event->product->should_alert,
            ],
            'subject_id' => $event->product->id,
            'subject_type' => InventoryProducts::class,
        ]);
    }

    public function handleCreatedUser(CreatedUserEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'oprettet bruger',
            'activity_label' => "Oprettet bruger {$event->user->name}",
            'activity_data' => (object) [
                'user_id' => $event->user->id,
                'username' => $event->user->username,
            ],
            'subject_id' => $event->user->id,
            'subject_type' => User::class,
        ]);
    }

    public function handleDeletedUser(DeletedUserEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'slettet bruger',
            'activity_label' => "Slettet bruger {$event->user->name}",
            'activity_data' => (object) [
                'user_id' => $event->user->id,
                'username' => $event->user->username,
            ],
            'subject_id' => $event->user->id,
            'subject_type' => User::class,
        ]);
    }

    public function handleUpdatedUser(UpdatedUserEvent $event): void
    {
        Activity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'opdateret bruger',
            'activity_label' => "Opdateret bruger {$event->user->name}",
            'activity_data' => (object) [
                'user_id' => $event->user->id,
                'username' => $event->user->username,
            ],
            'subject_id' => $event->user->id,
            'subject_type' => User::class,
        ]);
    }

    public function handleBulkDeletedUsers(BulkDeletedUsersEvent $event): void
    {
        foreach ($event->users as $user) {
            Activity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Masse sletning af brugere',
                'activity_label' => "Slettet bruger {$user->name}",
                'activity_data' => (object) [
                    'user_id' => $user->id,
                    'username' => $user->username,
                ],
                'subject_id' => $user->id,
                'subject_type' => User::class,
            ]);
        }
    }

    public function handleBulkUpdatedUsers(BulkUpdatedUsersEvent $event): void
    {
        $users = User::whereIn('id', $event->userIds)->get();
        foreach ($users as $user) {
            Activity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Masse opdatering af brugere',
                'activity_label' => "Opdateret bruger {$user->name}",
                'activity_data' => (object) [
                    'user_id' => $user->id,
                    'username' => $user->username,
                ],
                'subject_id' => $user->id,
                'subject_type' => User::class,
            ]);
        }
    }
}
