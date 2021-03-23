# axe-site-api
Write a Drupal8/9 custom module to alter the existing form on 'admin/config/system/site-information' without using hook form alter.
Add a text field to the above config form to save the api key in the config used by the form.
Create a rest resource to expose the node data in JSON based on node id which should be authenticated by the API key saved in the above form config.
E.g.
REST API should look similar to '/page_json/{api_key}/{nid}' where api_key is the api key used for authentication(saved in the system site config)
and nid is the node id of the node for which JSON response should be returned.
