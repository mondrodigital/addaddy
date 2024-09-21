document.addEventListener('DOMContentLoaded', function() {
    const dock = document.querySelector('.dock');
    const dockItems = document.querySelectorAll('.dock-item');

    function updateDockItemSize(item, distance) {
        const maxScale = 1.5;
        const minScale = 1;
        const scaleFactor = Math.max(minScale, Math.min(maxScale, 1 + (1 - distance) * (maxScale - minScale)));
        item.style.transform = `scale(${scaleFactor})`;
    }

    function handleMouseMove(e) {
        const dockRect = dock.getBoundingClientRect();
        const mouseX = e.clientX - dockRect.left;

        dockItems.forEach((item) => {
            const itemRect = item.getBoundingClientRect();
            const itemX = itemRect.left + itemRect.width / 2 - dockRect.left;
            const distance = Math.abs(mouseX - itemX) / (dockRect.width / 2);
            updateDockItemSize(item, distance);
        });
    }

    dock.addEventListener('mousemove', handleMouseMove);
    dock.addEventListener('mouseleave', () => {
        dockItems.forEach(item => item.style.transform = 'scale(1)');
    });
});