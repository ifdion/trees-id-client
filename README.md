# Trees ID Client


Display formatted Trees ID data using WordPress shortcode. There are two shortcode available [trees-id] to display program, project block or lot data and [trees-id-view-tree] to display tree map. Each shortcode accept several attributes to determine which data to be shown. Although you have added an attribute to determine which one to be displayed, the data can be overridden by $_REQUEST value with the attribute listed below.

## Displaying Program, Project, Block or Lot

```
[trees-id]
```

### Available  Attributes

 - `program_id=“value” ` Display Program data. Use the Trees ID program id as the value.
 - `project_id=“value” ` Display Project data. Use the Trees ID project id as the value.
 - `block_id=“value” ` Display Block data. Use the Trees ID block id as the value.
 - `lot_id=“value” ` Display Lot data. Use the Trees ID lot id as the value.

### Template Attributes
 
 - `template=“value”` Add a custom template file URL to be used, replacing the default theme. The template URL should be relative to stylesheet directory.

### Example

```
[trees-id program_id=“1” template=“trees-id-client/custom-project-front-end.php"]
```

This will display the Trees ID Program with the ID 1, using the file available on themes/your-theme/trees-id-client/custom-project-front-end.php.

## Displaying Single or Multiple Trees Map

```
[trees-id-view-tree]
```

### Available Attributes for Multiple Trees Map
 
 - `lot_id=“value”` Display trees of a Lot. Use the Lot ID lot id as the value
 - `donatur_email=“value”` Display trees adopted by a donor. Use the donor email as the value
 - `code=“value”` Display trees by tree code. Use the tree code as the value
 - `invoice=“value”` Display trees by invoice. Use the tree invoice as the value
 - `nohp=“value”` Display trees adopted by a donor. Use the donor phone number as the value
 - `affiliate=“value”` Display trees adopted through an affiliate. Use the affiiliate email as the value

### Available Attributes for Single Tree Map
 - `tree_offset=“value”` Display trees of a lot, by offset. Use the tree offset as the value. lot_id attribute are required in conjuction with this attribute.
 - `single_id=“value”` Display trees of a lot, by lot id. Use the tree id as the value.

### Template Attributes
 - `template=“value”` Add a custom template file URL to be used, replacing the default theme. The template URL should be relative to stylesheet directory


### Example

```
[trees-id-view-tree lot_id=“1” ]
```

This will display the of trees in Trees ID Lot with the ID 1


##Note on Template Files

The default template to display the data are available inside the `template` directory. To override the default, you can add a directory with the name `trees-id-client`  in your theme directory and copy the content inside the default template directory. The file in the theme directory will be used instead of the one on the plugin.

To display a customized template (e.g for the home page) you can over ride the template on the plugin using the `template` attribute inside the shortcode.
