$(document).ready(function(){
    var institutesViewModel = function(){
        return new function(){
            var self = this;
            self.errors = new errors();

            self.initial = {
                institutes: ko.observableArray([]),
                institute: {
                    id: ko.observable(0),
                    name: ko.observable(''),
                    description: ko.observable('')
                }
            };
            self.current = {
                institute: ko.observable(self.initial.institute),
                profiles: ko.observableArray([]),
                plans: ko.observableArray([])
            };
            self.actions = {
                show: {
                    institute: function(data){
                        var isCurrent =  data.id() === self.current.institute().id();
                        if (isCurrent) {
                            self.current.institute(self.initial.institute);
                            return;
                        }
                        self.current.institute.copy(data);
                        self.get.profiles();
                    },
                    plans: function(data){
                        self.get.plans(data.id());
                        commonHelper.modal.open('#show-plans-modal');
                    }
                },
                moveTo: {
                    group: function(data){
                        var url = '/admin/groups/' + data.id();
                        window.location.href = url;
                    },
                    plan: function(){}
                }
            };
            self.get = {
                institutes: function(){
                    var requestOptions = {
                        url: '/api/institutes',
                        errors: self.errors,
                        successCallback: function(data){
                            self.initial.institutes(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                profiles: function(){
                    var requestOptions = {
                        url: '/api/institute/'+ self.current.institute().id() + '/profiles',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.profiles(data());
                        }
                    };
                    $ajaxget(requestOptions);
                },
                plans: function(id){
                    var requestOptions = {
                        url: '/api/profile/'+ id + '/plans',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.plans(data());
                        }
                    };
                    $ajaxget(requestOptions);
                }
            };

            self.get.institutes();

            return {
                current: self.current,
                initial: self.initial,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(institutesViewModel());
});