# Toast Notification Testing Guide

## How Toast Notifications Work

The toast notification system uses:
1. **Livewire 3** to dispatch browser events
2. **Alpine.js** to listen and display toasts
3. **Tailwind CSS** for styling

## Event Flow

1. Livewire component calls: `$this->dispatch('toast', ['message' => '...', 'type' => 'success'])`
2. Livewire dispatches a browser event: `window.dispatchEvent(new CustomEvent('toast', { detail: {...} }))`
3. Alpine.js component listens via `@toast.window` and `window.addEventListener('toast', ...)`
4. Toast is added to the `toasts` array
5. Toast displays for 5 seconds, then auto-removes

## Testing Toast Notifications

### Manual Testing Steps

1. **Test Checkout Success**:
   - Go to `/library/circulation/checkout`
   - Enter a valid barcode
   - Select a user
   - Click "Checkout Book"
   - Should see green success toast

2. **Test Checkout Error**:
   - Go to `/library/circulation/checkout`
   - Enter invalid barcode
   - Select a user
   - Click "Checkout Book"
   - Should see red error toast

3. **Test Place Hold**:
   - Go to `/library/books/{id}` (book with no available copies)
   - Click "Place Hold"
   - Should see green success toast

4. **Test Renew Loan**:
   - Go to `/library/my-loans`
   - Click "Renew" on a loan
   - Should see green success toast

5. **Test Return Book**:
   - Go to `/library/circulation/return`
   - Enter barcode of checked out book
   - Should see success toast

## Debugging Toast Issues

### Check Browser Console

Open browser DevTools (F12) and check:
1. **Console tab**: Look for JavaScript errors
2. **Network tab**: Verify Livewire requests complete
3. **Elements tab**: Check if toast container exists in DOM

### Verify Toast Container is Loaded

In browser console, run:
```javascript
// Check if toast container exists
document.querySelector('[x-data*="toasts"]')

// Check if Alpine.js is loaded
window.Alpine

// Manually trigger a toast (for testing)
window.dispatchEvent(new CustomEvent('toast', {
    detail: {
        message: 'Test toast',
        type: 'success'
    }
}));
```

### Common Issues

1. **Toasts not showing**:
   - Check if Alpine.js is loaded (Flux includes it)
   - Verify toast container is in layout
   - Check browser console for errors

2. **Message not displaying**:
   - Verify `x-text="toast.message"` is in template
   - Check if `toast.message` has value
   - Verify event detail structure

3. **Wrong styling**:
   - Check if Tailwind classes are compiled
   - Verify dark mode classes if using dark theme

## Expected Behavior

- ✅ Toast appears in top-right corner
- ✅ Toast has correct color based on type (green=success, red=error, blue=info, yellow=warning)
- ✅ Toast shows message text
- ✅ Toast auto-dismisses after 5 seconds
- ✅ Toast can be manually closed with X button
- ✅ Multiple toasts stack vertically
- ✅ Toasts animate in from right
- ✅ Toasts animate out when dismissed

## Toast Types

- `success`: Green background, for successful operations
- `error`: Red background, for errors
- `info`: Blue background, for informational messages
- `warning`: Yellow background, for warnings

## Implementation Details

### Toast Container Location
- File: `resources/views/components/toast-container.blade.php`
- Included in: `resources/views/components/layouts/app/sidebar.blade.php` (line 144)

### Livewire Dispatch Pattern
```php
$this->dispatch('toast', [
    'message' => 'Your message here',
    'type' => 'success' // or 'error', 'info', 'warning'
]);
```

### Alpine.js Event Listener
```javascript
@toast.window="addToast($event.detail)"
window.addEventListener('toast', (event) => {
    this.addToast(event.detail);
});
```

