(function() {
  var appendInstitute, appendProfile, getInstitutes, getProfiles, institutes, profiles, signBtnEvents, signInstituteEvents;

  institutes = '';

  profiles = '';

  getInstitutes = function() {
    return $.ajax('/api/institutes', {
      success: function(result) {
        institutes = JSON.parse(result);
        return appendInstitute();
      },
      error: function(error) {
        return alert(error);
      }
    });
  };

  getProfiles = function(id) {
    return $.ajax('/api/institute/' + id + '/profiles', {
      success: function(result) {
        profiles = JSON.parse(result);
        return appendProfile(id);
      },
      error: function(error) {
        return alert(error);
      }
    });
  };

  appendInstitute = function() {
    var dataItem, i, len, newInstitute, results;
    results = [];
    for (i = 0, len = institutes.length; i < len; i++) {
      dataItem = institutes[i];
      newInstitute = '<div class="institute" institute-id="' + dataItem.id + '"> <span>' + dataItem.name + '</span> </div> <div hidden class="profiles" parent-id="' + dataItem.id + '"></div>';
      $('.institutes').append(newInstitute);
      results.push(getProfiles(dataItem.id));
    }
    return results;
  };

  appendProfile = function(id) {
    var i, item, len, newProfile, profilesDiv;
    profilesDiv = $('.profiles[parent-id=' + id + ']');
    for (i = 0, len = profiles.length; i < len; i++) {
      item = profiles[i];
      newProfile = '<div class="profile" profile-id="' + item.id + '"> <span>' + item.name + '</span></br> <button class="tyle-btn">Учебные планы</button> <button btn-id="' + item.id + '" class="goToGroup tyle-btn tyle-btn-negative">Перейти к группам</button> </div>';
      profilesDiv.append(newProfile);
    }
    signBtnEvents();
    return signInstituteEvents();
  };

  signBtnEvents = function() {
    return $('.goToGroup').on('click', function(e) {
      window.location.href = '/admin/groups/' + $(this).attr('btn-id');
      return e.preventDefault();
    });
  };

  signInstituteEvents = function() {
    return $('.institute').on('click', function() {
      var profilesDiv;
      profilesDiv = '.profiles[parent-id=' + $(this).attr('institute-id') + ']';
      if ($(profilesDiv).is(':hidden')) {
        return $(profilesDiv).show();
      } else {
        return $(profilesDiv).hide();
      }
    });
  };

  $(function() {
    return getInstitutes();
  });

}).call(this);

//# sourceMappingURL=institutes-coffee.js.map
