# WordPress Settings UI

A library providing an object-oriented interface to the WordPress Settings API.

## Features

*   A WordPress plugin's settings page can be specified as an array of textual properties.
*   A plugin can provide additional custom controls by implementing the `Mundschenk\UI\Control` interface.
*   Standard controls included with the library:
    -   Checkbox (`Mundschenk\UI\Controls\Checkbox_Input`),
    -   Text field (`Mundschenk\UI\Controls\Text_Input`),
    -   Number field (`Mundschenk\UI\Controls\Number_Input`),
    -   Hidden field (`Mundschenk\UI\Controls\Hidden_Input`),
    -   Submit button (`Mundschenk\UI\Controls\Submit_Input`),
    -   Select box (`Mundschenk\UI\Controls\Select`),
    -   Plain text (`Mundschenk\UI\Controls\Display_Text`), and
    -   Text Area (`Mundschenk\UI\Controls\Textarea`).
