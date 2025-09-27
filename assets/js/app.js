// Simple UI helpers
function editProduct(p) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('edit_id').value = p.id;
    document.getElementById('edit_name').value = p.name;
    document.getElementById('edit_pieces').value = p.pieces_per_pack;
}
function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}
// Auto adjust sale price placeholder when unit type changes (client-friendly)
document.addEventListener('DOMContentLoaded', function(){
    var saleProduct = document.getElementById('sale_product');
    var unitType = document.getElementById('unit_type');
    if (saleProduct && unitType) {
        unitType.addEventListener('change', function(){ /* could update UI if needed */ });
    }
});
