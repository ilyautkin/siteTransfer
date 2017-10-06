siteTransfer.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'sitetransfer-panel-home', renderTo: 'sitetransfer-panel-home-div'
		}]
	});
	siteTransfer.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(siteTransfer.page.Home, MODx.Component);
Ext.reg('sitetransfer-page-home', siteTransfer.page.Home);