import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
    // console.log('in save.js');

    const { id, defaultID, showFaqID } = attributes;
    // console.log('in save.js id = ' + id);
    // console.log('in save.js defaultID = ' + defaultID);
    // console.log('in save.js showID = ' + showFaqID);

    if ( ! defaultID ) {
        return null;
    }

    let displayFAQ;

    if ( id ) {
        displayFAQ = id;
    } else {
        displayFAQ = defaultID;
    }

    // console.log('in save.js displayFAQ = ' + displayFAQ);

    return (
       <p { ...useBlockProps.save() }>von save.js { displayFAQ }</p>
    );
}