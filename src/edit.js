/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {useSelect} from '@wordpress/data';
import {InspectorControls, useBlockProps} from '@wordpress/block-editor';
import {PanelBody, TextControl, ToggleControl, SelectControl, RangeControl} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';


export default function Edit({attributes, setAttributes}) {
    const {
        category,
        tag,
        id,
        hstart,
        order,
        sort,
        lang,
        additional_class,
        color,
        style,
        load_open,
        expand_all_link,
        hide_title,
        hide_accordion,
        glossarystyle,
        glossary
    } = attributes;
    const blockProps = useBlockProps();
    const [categorystate, setSelectedCategories] = useState(['']);
    const [tagstate, setSelectedTags] = useState(['']);
    const [idstate, setSelectedIDs] = useState(['']);

    useEffect(() => {
        console.log('Test');
        setAttributes({
            category: category,
            tag: tag,
            id: id,
            hstart: hstart,
            order: order,
            sort: sort,
            lang: lang,
            additional_class: additional_class,
            color: color,
            style: style,
            load_open: load_open,
            expand_all_link: expand_all_link,
            hide_title: hide_title,
            hide_accordion: hide_accordion,
            glossarystyle: glossarystyle,
            glossary: glossary
        });
    }, [category, tag, id, hstart, order, sort, lang, additional_class, color, style, load_open, expand_all_link, hide_title, hide_accordion, glossarystyle, glossary, setAttributes]);

    const categories = useSelect((select) => {
        return select('core').getEntityRecords('taxonomy', 'faq_category', {
            per_page: -1,
            orderby: 'name',
            order: 'asc',
            status: 'publish',
            ['_fields']: 'id,name,slug'
        });
    }, []);

    const categoryoptions = [
        {
            label: __('all', 'rrze-faq'),
            value: ''
        }
    ];

    if (!!categories) {
        Object.values(categories).forEach(category => {
            categoryoptions.push({
                label: category.name,
                value: category.slug,
            });
        });
    }

    const tags = useSelect((select) => {
        return select('core').getEntityRecords('taxonomy', 'faq_tag', {
            per_page: -1,
            orderby: 'name',
            order: 'asc',
            status: 'publish',
            ['_fields']: 'id,name,slug'
        });
    }, []);

    const tagoptions = [
        {
            label: __('all', 'rrze-faq'),
            value: ''
        }
    ];

    if (!!tags) {
        Object.values(tags).forEach(tag => {
            tagoptions.push({
                label: tag.name,
                value: tag.slug,
            });
        });
    }

    const faqs = useSelect((select) => {
        return select('core').getEntityRecords('postType', 'faq', {
            per_page: -1,
            orderby: 'title',
            order: 'asc',
            status: 'publish',
            ['_fields']: 'id,title.rendered'
        });
    }, []);

    const faqoptions = [
        {
            label: __('all', 'rrze-faq'),
            value: 0
        }
    ];

    if (!!faqs) {
        Object.values(faqs).forEach(faq => {
            faqoptions.push({
                label: faq.title.rendered ? faq.title.rendered : __('No title', 'rrze-faq'),
                value: faq.id,
            });
        });
    }

    const langoptions = [
        {
            label: __('all', 'rrze-faq'),
            value: ''
        },
        {
            label: __('German', 'rrze-faq'),
            value: 'de'
        },
        {

            label: __('English', 'rrze-faq'),
            value: 'en'
        },
        {

            label: __('French', 'rrze-faq'),
            value: 'fr'
        },
        {

            label: __('Spanish', 'rrze-faq'),
            value: 'es'
        },
        {
            label: __('Russian', 'rrze-faq'),
            value: 'ru'
        },
        {
            label: __('Chinese', 'rrze-faq'),
            value: 'zh'
        }
    ];

    const glossaryoptions = [
        {
            label: __('none', 'rrze-faq'),
            value: ''
        },
        {
            label: __('Categories', 'rrze-faq'),
            value: 'category'
        },
        {
            label: __('Tags', 'rrze-faq'),
            value: 'tag'
        }
    ];

    const glossarystyleoptions = [
        {
            label: __('A - Z', 'rrze-faq'),
            value: 'a-z'
        },
        {
            label: __('Tagcloud', 'rrze-faq'),
            value: 'tagcloud'
        },
        {
            label: __('Tabs', 'rrze-faq'),
            value: 'tabs'
        },
        {
            label: __('-- hidden --', 'rrze-faq'),
            value: ''
        }
    ];

    const coloroptions = [
        {
            label: 'fau',
            value: 'fau'
        },
        {
            label: 'med',
            value: 'med'
        },
        {
            label: 'nat',
            value: 'nat'
        },
        {
            label: 'phil',
            value: 'phil'
        },
        {
            label: 'rw',
            value: 'rw'
        },
        {
            label: 'tf',
            value: 'tf'
        }
    ];

    const styleoptions = [
        {
            label: __('none', 'rrze-faq'),
            value: ''
        },
        {
            label: 'light',
            value: 'light'
        },
        {
            label: 'dark',
            value: 'dark'
        }
    ];

    const sortoptions = [
        {
            label: __('Title', 'rrze-faq'),
            value: 'title'
        },
        {
            label: __('ID', 'rrze-faq'),
            value: 'id'
        },
        {
            label: __('Sort field', 'rrze-faq'),
            value: 'sortfield'
        }
    ];

    const orderoptions = [
        {
            label: __('ASC', 'rrze-faq'),
            value: 'ASC'
        },
        {
            label: __('DESC', 'rrze-faq'),
            value: 'DESC'
        }
    ];

    // console.log('edit.js attributes: ' + JSON.stringify(attributes));

    const onChangeCategory = (newValues) => {
        setSelectedCategories(newValues);
        setAttributes({category: String(newValues)})
    };

    const onChangeTag = (newValues) => {
        setSelectedTags(newValues);
        setAttributes({tag: String(newValues)})
    };

    const onChangeID = (newValues) => {
        setSelectedIDs(newValues);
        setAttributes({id: String(newValues)})
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Filter', 'rrze-faq')}>
                    <SelectControl
                        label={__('Categories', 'rrze-faq')}
                        value={categorystate}
                        options={categoryoptions}
                        onChange={onChangeCategory}
                        multiple
                    />
                    <SelectControl
                        label={__('Tags', 'rrze-faq')}
                        value={tagstate}
                        options={tagoptions}
                        onChange={onChangeTag}
                        multiple
                    />
                    <SelectControl
                        label={__('FAQ', 'rrze-faq')}
                        value={idstate}
                        options={faqoptions}
                        onChange={onChangeID}
                        multiple
                    />
                    <SelectControl
                        label={__('Language', 'rrze-faq'
                        )}
                        options={langoptions}
                        onChange={(value) => setAttributes({lang: value})}
                    />

                </PanelBody>
            </InspectorControls>
            <InspectorControls group="styles">
                <PanelBody title={__('Styles', 'rrze-faq')}>
                    <SelectControl
                        label={__('Glossary content', 'rrze-faq')}
                        options={glossaryoptions}
                        onChange={(value) => setAttributes({glossary: value})}
                    />
                    <SelectControl
                        label={__('Glossary style', 'rrze-faq')}
                        options={glossarystyleoptions}
                        onChange={(value) => setAttributes({glossarystyle: value})}
                    />
                    <ToggleControl
                        checked={!!hide_accordion}
                        label={__('Hide accordion', 'rrze-faq')}
                        onChange={() => setAttributes({hide_accordion: !hide_accordion})}
                    />
                    <ToggleControl
                        checked={!!hide_title}
                        label={__('Hide title', 'rrze-faq')}
                        onChange={() => setAttributes({hide_title: !hide_title})}
                    />
                    <ToggleControl
                        checked={!!expand_all_link}
                        label={__('Show "expand all" button', 'rrze-faq')}
                        onChange={() => setAttributes({expand_all_link: !expand_all_link})}
                    />
                    <ToggleControl
                        checked={!!load_open}
                        label={__('Load website with opened accordions', 'rrze-faq')}
                        onChange={() => setAttributes({load_open: !load_open})}
                    />
                    <SelectControl
                        label={__('Color', 'rrze-faq')}
                        options={coloroptions}
                        onChange={(value) => setAttributes({color: value})}
                    />
                    <SelectControl
                        label={__('Style', 'rrze-faq')}
                        options={styleoptions}
                        onChange={(value) => setAttributes({style: value})}
                    />
                    <TextControl
                        label={__(
                            'Additional CSS-class(es) for sourrounding DIV',
                            'rrze-faq'
                        )}
                        onChange={(value) => setAttributes({additional_class: value})}
                    />
                    <SelectControl
                        label={__('Sort', 'rrze-faq')}
                        options={sortoptions}
                        onChange={(value) => setAttributes({sort: value})}
                    />
                    <SelectControl
                        label={__('Order', 'rrze-faq')}
                        options={orderoptions}
                        onChange={(value) => setAttributes({order: value})}
                    />
                    <RangeControl
                        label={__('Heading starts with...', 'rrze-faq')}
                        onChange={(value) => setAttributes({hstart: value})}
                        min={2}
                        max={6}
                        initialPosition={2}
                    />
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <ServerSideRender
                    block="create-block/rrze-faq"
                    attributes={attributes}
                />
            </div>
        </>
    );
}