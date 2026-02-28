/**
 *  The list of containers that must use Morphdom to update their content
 *  @type {Array}
 */
const containersUsingMorphdom = [
    'motion/events/list',
];

/**
 *  The functions to execute after a container is reloaded
 */
const postReloadFunctions = {
    'motion/events/list': function () {
        // Re-initialize video lazy loading for events list
        initializeVideoLazyLoading();
        // Reload total media size for events date selector
        loadEventDateTotalMediaSize();
    }
};

/**
 *  Morphdom skip rules for specific containers
 *  @type {Object}
 */
const morphdomSkipRules = {
    'motion/events/list': [
        { element: 'VIDEO', skipIf: 'playing' },
        { element: 'VIDEO', skipIf: 'sameAttribute', attribute: 'poster-to-load' }
    ]
};

/**
 *  Default morphdom skip rules, apply to all containers
 *  @type {Array}
 */
const defaultMorphdomSkipRules = [
    { element: 'INPUT[type="checkbox"]', skipIf: 'checked' },
    { element: 'CANVAS', skipIf: 'always' }
];
