jQuery(document).ready(function() {
  var mdc = new MapDataColumn();

});

function MapDataColumn() {
  var self = this;
  self.addListeners();
}

MapDataColumn.prototype.addListeners = function() {
  var self = this;
  jQuery('.remove-butt').click(self.clickedRemove);
}

MapDataColumn.prototype.clickedRemove = function(e) {
  jQuery(e.currentTarget).parents('.postmeta_row').remove();
}