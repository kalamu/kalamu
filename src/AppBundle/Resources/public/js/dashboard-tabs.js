$.widget( "kalamu.kalamuDashboardTabs", {

    options: {
        dashboard_api: null,    // base URL for API
        identifier: null,       // identifier for storage of tabs config
        dashboards: null,       // list of dashboards (tabs)
        explorerWidget: null,   // widget explorer
        enable_widget: true,    // enable widget on dashboards
        explorerSection: null,  // section explorer
        enable_section: true,   // enable sections on dashboards
        currentDashboard: null, // dashboard actuellement visible
        currentDashboardId: null// id du dashboard courant
    },

    _create: function() {
        this.element.addClass('tab-block kalamu-dashboard-tab');

        // Stop edit when tab is not active
        this.element.on('hide.bs.tab', $.proxy(function(e){
            dashboard = this.element.find($(e.target).attr('href')).find('>.dashboard');
            if(dashboard.kalamuDashboard('option', 'editing')){
                dashboard.kalamuDashboard('option', 'editing', false);
                dashboard.parent().find('.dashboard-options').hide();
            }
        }, this));
        this.element.on('shown.bs.tab', $.proxy(function(e){
            this.options.currentDashboard = $( $(e.target).attr('href') );
            this.options.currentDashboardId = $(e.target).data('dashboard-id');
            this.loadDashboard();
        }, this));

        $.ajax({
            url: this.options.dashboard_api+this.options.identifier,
            method: 'GET',
            dataType: 'json',
            context: this,
            success: function(datas){
                if(!datas || !typeof datas.dashboards === 'object' || !Object.keys(datas.dashboards).length){
                    this.options.dashboards = {};
                    this.options.dashboards[this._getUniqKey()] = 'Dashboard';
                }else{
                    this.options.dashboards = datas.dashboards;
                }
                this.refresh();
            }
        });
    },

    refresh: function(){
        this.element.find('>*').remove();

        this.element.append('<ul class="nav nav-tabs">');
        this.element.append('<div class="tab-content pv10"></div>');
        $.each(this.options.dashboards, $.proxy(function(ul, content, id, libelle){

            var editLink = $('<span title="Edit this dashboard" class="btn btn-xs ml10 edit-dashboard-link"><i class="fa fa-gear text-muted"></i></span>');
            $('<li><a href="#tb_'+id+'" data-toggle="tab" data-dashboard-id="'+id+'">'+libelle+'</a></li>').find('>a').append(editLink).parent().appendTo( ul );
            this._on(editLink, {click: this.editDashboard});

            content.append('<div id="tb_'+id+'" class="tab-pane"><div class="row dashboard-options mb10"></div><div class="dashboard container-fluid pn"></div></div>');

            var nameInput = $('<div class="col-md-3"><input type="text" name="dashboard_title" class="form-control input-sm"></div>');
            var saveLink = $('<a title="Save" class="btn btn-sm mr10 btn-success"><i class="fa fa-save"></i> Save</a>');
            var cancelLink = $('<a title="Cancel" class="btn btn-sm mr10 btn-default"><i class="fa fa-undo"></i> Cancel</a>');
            var deleteLink = $('<a title="Remove this tab" class="btn btn-sm mr10 btn-danger"><i class="fa fa-trash"></i> Remove</a>');
            $('#tb_'+id).find('>.dashboard-options').hide().append(nameInput).append( $('<div class=col-md-9">').append(saveLink).append(cancelLink).append(deleteLink) );
            this._on(saveLink, {click: this.saveDashboard});
            this._on(cancelLink, {click: this.undoDashboard});
            this._on(deleteLink, {click: this.removeDashboard});

            var options = {};
            if(this.options.explorerWidget){
                options.explorerWidget = this.options.explorerWidget;
                options.enable_widget = this.options.enable_widget;
            }
            if(this.options.explorerSection){
                options.explorerSection = this.options.explorerSection;
                options.enable_section = this.options.enable_section;
            }

            content.find('#tb_'+id+'>.dashboard').kalamuDashboard(options);
        }, this, this.element.find('.nav-tabs'), this.element.find('.tab-content')));

        addDashboardButton = $('<a href="#" title="Add a tab"><i class="fa fa-plus"></i></a>');
        this.element.find('.nav-tabs').append( $('<li>').append(addDashboardButton) );
        this._on(addDashboardButton, {click: this.addDashboard});

        this.element.find('.nav-tabs a:first').tab('show');
    },

    /**
     * Download datas of the dashboard
     * @returns {undefined}
     */
    loadDashboard: function(){
        $.ajax({
            url: this.options.dashboard_api+this.options.identifier+'.'+this.options.currentDashboardId,
            method: 'GET',
            dataType: 'json',
            success: $.proxy(function(datas){
                if(datas && Object.keys(datas).length){
                    this.find('>.dashboard').kalamuDashboard('import', datas);
                }
            }, this.options.currentDashboard)
        });
    },

    /**
     * Start editing the current dashboard
     * @returns {undefined}
     */
    editDashboard: function(){
        this.options.currentDashboard.find('>.dashboard').kalamuDashboard('option', 'editing', true);
        this.options.currentDashboard.find('>.dashboard-options').show()
                .find('input[name=dashboard_title]').val(this.options.dashboards[this.options.currentDashboardId]);
    },

    /**
     * Save the dashboard configuration
     * @returns {undefined}
     */
    saveDashboard: function(){
        this.options.currentDashboard.find('>.dashboard-options .fa-save').removeClass('fa-save').addClass('fa-refresh fa-spin');
        this.options.dashboards[this.options.currentDashboardId] = this.options.currentDashboard.find('>.dashboard-options input[name=dashboard_title]').val();
        this.element.find('>.nav-tabs a[data-dashboard-id="'+this.options.currentDashboardId+'"]').get(0).childNodes[0].nodeValue = this.options.dashboards[this.options.currentDashboardId];
        this._saveDashboards();

        $.ajax({
            url: this.options.dashboard_api+this.options.identifier+'.'+this.options.currentDashboardId,
            method: 'POST',
            dataType: 'json',
            data: this.options.currentDashboard.find('>.dashboard').kalamuDashboard('export'),
            context: this,
            success: function(datas){
                this.options.currentDashboard.find('>.dashboard').kalamuDashboard('option', 'editing', false);
                this.options.currentDashboard.find('>.dashboard-options .fa-refresh').removeClass('fa-refresh fa-spin').addClass('fa-save');
                this.options.currentDashboard.find('>.dashboard-options').hide();
            }
        });
    },

    /**
     * Cancel dashboard edition
     * @returns {undefined}
     */
    undoDashboard: function(){
        this.options.currentDashboard.find('>.dashboard').kalamuDashboard('option', 'editing', false);
        this.options.currentDashboard.find('>.dashboard-options').hide();
        this.loadDashboard();
    },

    /**
     Remove the dashboard
     * @returns {undefined}
     */
    removeDashboard: function(){
        this._confirm("Dashboard removing", "Are you sure to want to remove this dashboard?", $.proxy(function(){
            $.ajax({
                url: this.options.dashboard_api+this.options.identifier+'.'+this.options.currentDashboardId,
                method: 'DELETE',
                dataType: 'json',
                context: this,
                success: function(datas){
                    delete this.options.dashboards[this.options.currentDashboardId];
                    if(!Object.keys(this.options.dashboards).length){
                        this.options.dashboards[this._getUniqKey()] = 'Dashbaord';
                    }

                    this._saveDashboards();
                    this.refresh();
                }
            });
        }, this));
    },

    addDashboard: function(e){
        e.preventDefault();

        modal = this._getModal();
        modal.find('.modal-title').text("Add a new tab");
        modal.find('.modal-body').html("<p>Enter the name of the tab:</p>");

        form = $('<form action="#"><div class="row"><div class="col-md-12">\n\
                <div class="form-group">\n\
                    <label class=" control-label col-sm-3 required" for="new_dashboard_name">Name <span class="asterisk">*</span></label>\n\
                    <div class="col-sm-9"><input type="text" value="" class="form-control" required="required" name="label" /></div>\n\
                </div></div></div></form>');
        modal.find('.modal-body').append(form);
        form.on('submit', $.proxy(function(e){
            e.preventDefault();
            if(e.target.checkValidity()){
                this.root.options.dashboards[this.root._getUniqKey()] = this.modal.find('input[name="label"]').val();
                this.root._saveDashboards();

                this.root.refresh();
                this.root.element.find('.nav-tabs a[data-dashboard-id]').last().tab('show');

                this.modal.on('hidden.bs.modal', function(){ $(this).remove(); });
                this.modal.modal('hide');
            }
        }, {root: this, modal: modal}));

        cancel = $('<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>');
        add = $('<button type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add</button>');
        modal.find('.modal-footer').text('').append(cancel).append(add);

        add.on('click', $.proxy(function(e){
            this.find('form').trigger('submit');
        }, modal));

        modal.modal('show');
    },

    /**
     * Save the dashboard list
     * @returns {undefined}
     */
    _saveDashboards: function(){
        $.ajax({
            url: this.options.dashboard_api+this.options.identifier,
            method: 'POST',
            dataType: 'json',
            data: {dashboards: this.options.dashboards}
        });
    },

    /**
     * Modal configuration window
     * @param {type} title
     * @param {type} message
     * @param {type} callback
     * @returns {undefined}
     */
    _confirm: function(title, message, callback){

        modal = this._getModal();

        modal.find('.modal-title').text(title);
        modal.find('.modal-body').html("<p>"+message+"</p>");

        cancel = $('<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>');
        confirm = $('<button type="button" class="btn btn-info"><i class="fa fa-check"></i> Confirm</button>');
        modal.find('.modal-footer').text('').append(cancel).append(confirm);

        confirm.on('click', $.proxy(function(){
            this.on('hidden.bs.modal', function(){ $(this).remove(); });
            this.modal('hide');
        }, modal));
        confirm.on('click', callback);

        modal.modal('show');
    },

    /**
     * Get new empty modal
     * @returns {window.$|$}
     */
    _getModal: function(){

        var modal =  $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content">\n\
                            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close">\n\
                            <span aria-hidden="true">&times;</span></button><h4 class="modal-title"></h4></div><div class="modal-body">\n\
                            </div><div class="modal-footer"></div></div></div></div>');

        $('body').append(modal);

        return modal;
    },

    /**
     * Generate a uniq string
     * @returns {String}
     */
    _getUniqKey: function(){
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for( var i=0; i < 10; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }

});