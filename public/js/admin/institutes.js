$(document).ready(function(){
    var institutesViewModel = function(){
        return new function(){
            var self = this;
            self.page = ko.observable(menu.admin.main);
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
                profileId: ko.observable(null),
                plans: ko.observableArray([]),
                plan: {
                    name: ko.observable(''),
                    mode: ko.observable(state.none)
                }
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
                        self.current.profileId(data.id());
                        self.get.plans();
                        commonHelper.modal.open('#show-plans-modal');
                    }
                },
                moveTo: {
                    group: function(data){
                        var url = '/admin/groups/' + data.id();
                        window.location.href = url;
                    },
                    plan: function(data){
                        window.location.href = '/admin/studyplan/' + data.id();
                    }
                },
                plan: {
                    create: function(){
                        self.actions.plan.cancel();
                        self.current.plan.mode(state.create);
                    },
                    cancel: function(){
                        self.current.plan.name('');
                        self.current.plan.mode(state.none);
                    },
                    approve: function(){
                        self.post.plan();
                    }
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
                plans: function(){
                    var requestOptions = {
                        url: '/api/profile/'+ self.current.profileId() + '/plans',
                        errors: self.errors,
                        successCallback: function(data){
                            self.current.plans(data());
                        }
                    };
                    $ajaxget(requestOptions);
                }
            };
            self.post = {
                plan: function(){
                    var requestOptions = {
                        url: '/api/plan/create',
                        errors: self.errors,
                        data: JSON.stringify({
                            studyPlan: {name: self.current.plan.name()},
                            profileId: self.current.profileId()
                        }),
                        successCallback: function(){
                            self.actions.plan.cancel();
                            self.get.plans()
                        }
                    };
                    $ajaxpost(requestOptions);
                }
            };

            self.get.institutes();

            return {
                page: self.page,
                current: self.current,
                initial: self.initial,
                actions: self.actions,
                errors: self.errors
            };
        };
    };

    ko.applyBindings(institutesViewModel());
});