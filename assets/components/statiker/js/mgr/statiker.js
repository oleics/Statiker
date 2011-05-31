
var Statiker = function(config) {
    config = config || {};
    /* Grid configuration options */
    Ext.applyIf(config, {
    });
    
    /* Class parent constructor */
    Statiker.superclass.constructor.call(this,config);
};
Ext.extend(Statiker,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('statiker',Statiker);
Statiker = new Statiker();
