var siteTransfer = function (config) {
	config = config || {};
	siteTransfer.superclass.constructor.call(this, config);
};
Ext.extend(siteTransfer, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('sitetransfer', siteTransfer);

siteTransfer = new siteTransfer();