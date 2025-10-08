
// Animation sur les boutons
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.btn-ajout, .btn-users').forEach(function(btn) {
		btn.addEventListener('mouseenter', function() {
			btn.style.transform = 'scale(1.08)';
			btn.style.boxShadow = '0 2px 12px rgba(45,125,210,0.15)';
		});
		btn.addEventListener('mouseleave', function() {
			btn.style.transform = 'scale(1)';
			btn.style.boxShadow = 'none';
		});
	});
});
