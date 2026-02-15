const currentUserId = window.App?.user?.id || window.authUserId;

function handleResourceReload(e) {
  window.ResourceRegistry?.[e.resource]?.();
}

// 🔹 PRIVATE CHANNEL (notify author)
window.Echo.private(`App.Models.User.${currentUserId}`).listen('.system.resource.updated', e => {
  if (e.message) {
    showToast('success', e.message);
  }

  handleResourceReload(e);
});

// 🔹 PUBLIC CHANNEL (update all users except author)
window.Echo.channel('system.resource.updated').listen('.system.resource.updated', e => {
  if (e.performedBy !== currentUserId) {
    handleResourceReload(e);
  }
});
