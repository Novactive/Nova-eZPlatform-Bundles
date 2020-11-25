import React from 'react';
import ReactDOM from 'react-dom';
import algoliasearch from 'algoliasearch/lite';

import {
    InstantSearch,
    Configure,
    connectSearchBox,
    connectHits,
    Stats,
    connectHitsPerPage,
    connectPagination,
    connectSortBy,
    Highlight,
    connectRefinementList
} from 'react-instantsearch-dom';

const NovaEzAlgoliaSearch = ({ replicas, config, query }) => {
    const searchClient = algoliasearch(
        JSON.parse(config).app_id,
        JSON.parse(config).api_key
    );

    const queryParameters = JSON.parse(query);

    const indexName =
        JSON.parse(config).index_name_prefix + '-' + queryParameters.language;

    const fullIndexName = (indexName, replica) => {
        if (replica !== null) {
            indexName += '-' + replica;
        }
        return indexName;
    };

    const sortByItems = (indexName, replicas) => {
        const items = [{ value: indexName, label: 'Default' }];
        for (const index in replicas) {
            items.push({
                value: indexName + '-' + replicas[index].key,
                label: replicas[index].label
            });
        }
        return items;
    };

    return (
        <div>
            <InstantSearch
                searchClient={searchClient}
                indexName={fullIndexName(indexName, queryParameters.replica)}>
                <Configure
                    {...queryParameters.requestOptions}
                    page={queryParameters.page}
                    hitsPerPage={queryParameters.hitsPerPage}
                    query={queryParameters.term}
                    filters={queryParameters.filtersString}
                />
                <div className='container pt-5'>
                    <div className='row'>
                        <div className='col-3'>
                            <h3>
                                Language:{' '}
                                <span className='badge badge-secondary'>
                                    {queryParameters.language}
                                </span>
                            </h3>
                            <Stats />
                        </div>
                        <div className='col-3'>
                            <CustomSearchBox />
                        </div>
                        <div className='col-3'>
                            <CustomHitsPerPage
                                defaultRefinement={queryParameters.hitsPerPage}
                                items={[
                                    {
                                        value: queryParameters.hitsPerPage,
                                        label:
                                            queryParameters.hitsPerPage +
                                            ' hits per page'
                                    }
                                ]}
                            />
                        </div>
                        <div className='col-3'>
                            <CustomSortBy
                                defaultRefinement={fullIndexName(
                                    indexName,
                                    queryParameters.replica
                                )}
                                items={sortByItems(
                                    indexName,
                                    JSON.parse(replicas)
                                )}
                            />
                        </div>
                    </div>
                    <div className='row'>
                        <div className='col-3'>
                            {queryParameters.facets.map(item => (
                                <div
                                    className='accordion'
                                    id='customRefinementList'
                                    key={item.key}>
                                    <div className='card'>
                                        <div
                                            className='card-header'
                                            id='headingOne'>
                                            <h2 className='mb-0'>
                                                <button
                                                    className='btn btn-link btn-block text-left font-weight-bold'
                                                    type='button'
                                                    data-toggle='collapse'
                                                    data-target='#collapseOne'
                                                    aria-expanded='true'
                                                    aria-controls='collapseOne'>
                                                    <h5 className={'mb-0'}>
                                                        <span className='badge badge-primary'>
                                                            {item.label}
                                                        </span>
                                                    </h5>
                                                </button>
                                            </h2>
                                        </div>

                                        <div
                                            id='collapseOne'
                                            className='collapse show'
                                            aria-labelledby='headingOne'
                                            data-parent='#customRefinementList'>
                                            <div className='card-body'>
                                                <CustomRefinementList
                                                    attribute={item.key}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <div className='col-9'>
                            <CustomHits />
                        </div>
                    </div>
                    <div className='row'>
                        <div className='col-3'>&nbsp;</div>
                        <div className='col-9'>
                            <CustomPagination />
                        </div>
                    </div>
                </div>
            </InstantSearch>
        </div>
    );
};

const HitsPerPage = ({ items, currentRefinement, refine, createURL }) => {
    const defaultValue = items[0].value;
    const biggerValue = defaultValue * 2;
    items.unshift({
        value: biggerValue,
        label: biggerValue + ' hits per page'
    });
    if (defaultValue > 1) {
        const lessValue = Math.floor(defaultValue / 2);
        items.push({ value: lessValue, label: lessValue + ' hits per page' });
    }

    return (
        <div className='dropdown'>
            <a
                className='btn btn-secondary dropdown-toggle'
                href='#'
                role='button'
                id='hitsPerPageLink'
                data-toggle='dropdown'
                aria-haspopup='true'
                aria-expanded='false'>
                {currentRefinement + ' hits per page'}
            </a>
            <div className='dropdown-menu' aria-labelledby='hitsPerPageLink'>
                {items.map(item => (
                    <a
                        key={item.value}
                        className={'dropdown-item'}
                        href={createURL(item.value)}
                        onClick={event => {
                            event.preventDefault();
                            refine(item.value);
                        }}>
                        {item.label}
                    </a>
                ))}
            </div>
        </div>
    );
};

const CustomHitsPerPage = connectHitsPerPage(HitsPerPage);

const SortBy = ({ items, refine, currentRefinement, createURL }) => {
    let currentLabel = items[0].label;
    items.map(item => {
        if (item.value === currentRefinement) {
            currentLabel = item.label;
        }
    });

    return (
        <div className='dropdown'>
            <a
                className='btn btn-info dropdown-toggle'
                href='#'
                role='button'
                id='sortByLink'
                data-toggle='dropdown'
                aria-haspopup='true'
                aria-expanded='false'>
                {currentLabel}
            </a>
            <div className='dropdown-menu' aria-labelledby='sortByLink'>
                {items.map(item => (
                    <a
                        key={item.value}
                        className={'dropdown-item'}
                        href={createURL(item.value)}
                        onClick={event => {
                            event.preventDefault();
                            refine(item.value);
                        }}>
                        {item.label}
                    </a>
                ))}
            </div>
        </div>
    );
};

const CustomSortBy = connectSortBy(SortBy);

const RefinementList = ({
    items,
    isFromSearch,
    refine,
    searchForItems,
    createURL
}) => (
    <ul className={'list-group'}>
        {items.map(item => (
            <li key={item.label} className={'list-group-item'}>
                <a
                    href={createURL(item.value)}
                    style={{ fontWeight: item.isRefined ? 'bold' : '' }}
                    onClick={event => {
                        event.preventDefault();
                        refine(item.value);
                    }}>
                    {isFromSearch ? (
                        <Highlight attribute='label' hit={item} />
                    ) : (
                        item.label
                    )}{' '}
                    ({item.count})
                </a>
            </li>
        ))}
    </ul>
);

const CustomRefinementList = connectRefinementList(RefinementList);

const Hit = ({ hit }) => {
    /**
     * You may not have title_value_s and description_value_s
     */
    return (
        <div className='card'>
            <img
                src={hit[hit.content_type_identifier_s + '_image_uri_s']}
                className='card-img-top'
                alt={hit[hit.content_type_identifier_s + '_title_value_s']}
            />
            <div className='card-body'>
                <h5 className='card-title'>
                    {hit[hit.content_type_identifier_s + '_title_value_s']}
                </h5>
                <p className='card-text'>
                    {
                        hit[
                            hit.content_type_identifier_s +
                                '_description_value_s'
                        ]
                    }
                </p>
                <p className='card-text'>
                    <small className='text-muted'>
                        Last updated {hit.content_modification_date_dt}
                    </small>
                </p>
            </div>
        </div>
    );
};

const Hits = ({ hits }) => (
    <div className='row'>
        {hits.map(hit => (
            <div className='col-4 mb-4' key={hit.objectID}>
                <Hit hit={hit} />
            </div>
        ))}
    </div>
);

const CustomHits = connectHits(Hits);

const SearchBox = ({ currentRefinement, isSearchStalled, refine }) => (
    <form noValidate action='' role='search'>
        <div className='form-group'>
            <input
                type={'search'}
                value={currentRefinement}
                className='form-control'
                onChange={event => refine(event.currentTarget.value)}
                placeholder='Search'
                aria-label='Search'
                aria-describedby='button-addon2'
            />
            {isSearchStalled ? 'My search is stalled' : ''}
        </div>
    </form>
);

const CustomSearchBox = connectSearchBox(SearchBox);

const Pagination = ({ currentRefinement, nbPages, refine, createURL }) => (
    <nav aria-label='Page navigation'>
        <ul className='pagination mt-3'>
            {new Array(nbPages).fill(null).map((_, index) => {
                const page = index + 1;

                return (
                    <li
                        key={index}
                        className={
                            'page-item' +
                            (currentRefinement === page && ' active')
                        }
                        {...(currentRefinement === page && {
                            'aria-current': 'page'
                        })}>
                        <a
                            href={createURL(page)}
                            className={'page-link'}
                            onClick={event => {
                                event.preventDefault();
                                refine(page);
                            }}>
                            {page}
                        </a>
                    </li>
                );
            })}
        </ul>
    </nav>
);

const CustomPagination = connectPagination(Pagination);

const container = document.getElementById('js-algolia-search-container');
ReactDOM.render(<NovaEzAlgoliaSearch {...container.dataset} />, container);
