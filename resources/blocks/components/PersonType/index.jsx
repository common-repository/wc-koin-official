import { registerBlockType } from '@wordpress/blocks';
import { Edit } from './edit';
import metadata from './block.json';
import './frontend';

registerBlockType(metadata, {
    edit: Edit,
});
