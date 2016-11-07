/**
 * Created by nyanjii on 11.10.16.
 */
$(document).ready(function(){
    var disciplinesViewModel = function(){
        return new function(){
            var self = this;

            self.disciplines = ko.observableArray([]);
            self.current = {
                discipline: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable(''),
                    abbreviation: ko.observable(''),
                    description: ko.observable('')
                }),
                profile: ko.observable({
                    profiles: ko.observableArray([]),
                    selected: ko.observableArray([]),
                    discipline: ko.observableArray([])
                }),
                themes: ko.observableArray([]),
                theme: ko.observable({
                    id: ko.observable(0),
                    name: ko.observable('')
                })
            };
            self.filter = {
                discipline: ko.observable(''),
                profile : ko.observable()
            };
            self.toggleCurrent = {
                fill: function(data){
                    self.current.discipline()
                        .id(data.id())
                        .name(data.name())
                        .abbreviation(data.abbreviation())
                        .description(data.description());
                },
                empty: function(){
                    self.current.discipline()
                        .id(0)
                        .name('')
                        .abbreviation('')
                        .description('');
                    self.current.profile().selected([]);
                },
                stringify: function(){
                    var edit = self.current.discipline();
                    var profiles = [];
                    var forpost = {
                        name: edit.name(),
                        abbreviation: edit.abbreviation(),
                        description: edit.description()
                    };
                    self.mode() === 'edit' ? forpost.id = edit.id() : null;
                    self.current.profile().selected().forEach(function(item){
                        profiles.push(item.id());
                    });
                    return JSON.stringify({discipline: forpost, profileIds: profiles});
                },
                setInitialProfiles: function(){
                    var discipline = self.current.profile().discipline();
                    var profiles = self.current.profile().profiles;
                    var selected = self.current.profile().selected;
                    var received = self.current.profile().received;
                    selected([]);
                    if (discipline.length){
                        discipline.forEach(function(disc){
                            var profile = profiles().find(function(item){
                                return  (item.id() == disc.profile_id()) ? item : null;
                            });

                            if (profile){
                                selected.push(profile);
                                received.push(profile);
                            }
                        });
                    }
                }
            };
            self.pagination = {
                currentPage: ko.observable(1),
                pageSize: ko.observable(10),
                itemsCount: ko.observable(1),
                totalPages: ko.observable(1),

                selectPage: function(page){
                    self.pagination.currentPage(page);
                    self.get.disciplines();
                },
                dotsVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total > 11 && index == total-1 && index > current + 2  ||total > 11 && index == current - 1 && index > 3)  {
                        return true;
                    }
                    return false;
                },
                pageNumberVisible: function(index){
                    var total = self.pagination.totalPages();
                    var current = self.pagination.currentPage();
                    if (total < 12 ||
                        index > (current - 2) && index < (current + 2) ||
                        index > total - 2 ||
                        index < 3) {
                        return true;
                    }
                    return false;
                },
            };
            self.mode = ko.observable('none');
            self.csed = {
                show: function(data){
                    if (self.mode() === 'none' || self.current.discipline().id() !== data.id()){
                        self.mode('info');
                        self.get.disciplineProfiles();
                        self.toggleCurrent.fill(data);
                        self.get.themes();
                        return;
                    }
                    self.mode('none');
                    self.toggleCurrent.empty();
                },
                startAdd: function(){
                    self.toggleCurrent.empty();
                    self.mode() === 'add' ? self.mode('none') : self.mode('add');
                },
                startUpdate: function(){
                    self.mode('edit');
                    self.toggleCurrent.setInitialProfiles();
                },
                startRemove: function(){
                    self.mode('delete');
                    self.toggleModal('#delete-modal', '');
                },
                update: function(){
                    var url = self.mode() === 'add' ? '/api/disciplines/create' : '/api/disciplines/update';
                    var json = self.toggleCurrent.stringify();
                    console.log(url + ' : ' + json);
                    self.post(url, json);
                },
                remove: function(){
                    self.toggleModal('#delete-modal', 'close');
                    var url = '/api/disciplines/delete/' + self.current.discipline().id();
                    self.post(url, '');
                },
                cancel: function(){
                    if (self.mode() === 'add'){
                        self.mode('none');
                        self.toggleCurrent.empty();
                        return;
                    }
                    self.mode('info');
                },
                theme: {
                    startAdd: function(){
                        console.log('add theme');
                        self.current.theme().id(0).name('');
                        self.toggleModal('#add-theme-modal', '');
                    },
                    add: function(){
                        var url = '/api/disciplines/themes/create';
                        var json = JSON.stringify({
                            theme: {
                                name: self.current.theme().name()
                            },
                            disciplineId: self.current.discipline().id()
                        });
                        console.log(url + ' : ' + json);
                        $.post(url, json, function(){
                            self.toggleModal('#add-theme-modal', 'close');
                            self.get.themes();
                        });
                    },
                    startRemove: function(data){
                        self.toggleModal('#remove-theme-modal', '');
                        self.current.theme().id(data.id()).name(data.name());

                    },
                    remove: function(){
                        var url = '/api/disciplines/themes/delete/' + self.current.theme().id();
                        $.post(url, function(){
                            self.toggleModal('#remove-theme-modal', 'close');
                            self.get.themes();
                        });
                    }
                }
            };

            self.moveTo = {
                theme: function(data){
                    window.location.href = '/admin/theme/' + data.id();
                },
                tests: function(data){
                    window.location.href = '/admin/tests/' + data.id();
                }
            };
            self.get = {
                disciplines: function(profileId){
                    var filter = self.filter;
                    var profile = 'profile=' + (filter.profile() ? filter.profile().id() : '');
                    var name = 'name=' + filter.discipline();
                    var page = 'page=' + self.pagination.currentPage();
                    var pageSize = 'pageSize=' + self.pagination.pageSize();
                    var url = '/api/disciplines/show?' + page + '&' + pageSize + '&' + name + '&' + profile;

                    $.get(url, function(response){
                        var result = ko.mapping.fromJSON(response);
                        self.disciplines(result.data());
                        self.pagination.itemsCount(result.count());
                    });
                },
                disciplineProfiles: function(){
                    var id = self.current.discipline().id();
                    if (id){
                        $.get('/api/disciplines/' + id + '/profiles', function(response){
                            self.current.profile().discipline(ko.mapping.fromJSON(response)());
                        });
                    }
                },
                profiles: function(){
                    $.get('/api/profiles', function(response){
                        self.current.profile().profiles(ko.mapping.fromJSON(response)());
                    });
                },
                themes: function(){
                    var url = '/api/disciplines/' + self.current.discipline().id() +'/themes';
                    $.get(url, function(response){
                        self.current.themes(ko.mapping.fromJSON(response)());
                    });
                }
            };
            self.get.disciplines();
            self.get.profiles();


            self.post = function(url, json){
                $.post(url, json, function(result){
                    self.mode('none');
                    self.toggleCurrent.empty();
                    self.get.disciplines();
                });
            };
            self.toggleModal = function(selector, action){
                $(selector).arcticmodal(action);
            };

            // SUBSCRIPTIONS
            self.pagination.itemsCount.subscribe(function(value){
                if (value){
                    self.pagination.totalPages(Math.ceil(
                        value/self.pagination.pageSize()
                    ));
                }
            });
            self.filter.discipline.subscribe(function(){
                self.get.disciplines();
            });
            self.filter.profile.subscribe(function(){
                self.get.disciplines();
            });

            return {
                disciplines: self.disciplines,
                pagination: self.pagination,
                current: self.current,
                moveTo: self.moveTo,
                mode: self.mode,
                csed: self.csed,
                filter: self.filter,
                toggleModal: self.toggleModal
            };
        };
    };

    ko.applyBindings(disciplinesViewModel());
});