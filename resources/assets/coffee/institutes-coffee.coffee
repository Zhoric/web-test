institutes = ''
profiles = ''

getInstitutes = () ->
  $.ajax '/api/institutes',
    success  : (result) ->
      institutes = JSON.parse(result)
      appendInstitute()
    error    : (error) -> alert(error)

getProfiles = (id) ->
  $.ajax '/api/institute/' + id + '/profiles',
    success  : (result) ->
      profiles = JSON.parse(result)
      appendProfile(id)
    error    : (error) -> alert(error)


appendInstitute = ->
  for dataItem in institutes
    newInstitute = '<div class="institute" institute-id="' + dataItem.id + '">
      <span>' + dataItem.name + '</span> </div>
      <div hidden class="profiles" parent-id="' + dataItem.id + '"></div>'
    $('.institutes').append(newInstitute)

    getProfiles(dataItem.id)


appendProfile = (id) ->
  profilesDiv = $('.profiles[parent-id='+ id + ']')
  for item in profiles
    newProfile = '<div class="profile" profile-id="' + item.id + '">
                    <span>' + item.name + '</span></br>
                    <button class="tyle-btn">Учебные планы</button>
                    <button btn-id="' + item.id + '" class="goToGroup tyle-btn tyle-btn-negative">Перейти к группам</button>
                </div>'
    profilesDiv.append(newProfile)
  signBtnEvents()
  signInstituteEvents()


signBtnEvents = ->
  $('.goToGroup').on('click', (e) ->
    window.location.href = '/admin/groups/' + $(this).attr('btn-id')
    e.preventDefault()
  )

signInstituteEvents = () ->
  $('.institute').on('click', ->
    profilesDiv = '.profiles[parent-id='+ $(this).attr('institute-id') + ']';

    if $(profilesDiv).is(':hidden')
      $(profilesDiv).show()
    else
      $(profilesDiv).hide()
  )

$ ->
  getInstitutes()