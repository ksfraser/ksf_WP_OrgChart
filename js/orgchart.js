/**
 * KSF OrgChart Interactive JavaScript Handler
 * WordPress ESS Adapter - Org Chart Viewer
 */

(function($) {
    'use strict';

    const KSF_ORGCHART = {
        config: {},
        state: {
            centerNodeId: null,
            context: 'hrm',
            projectId: null,
            levelsUp: 2,
            levelsDown: 2,
            scale: 1.0,
            isDragging: false,
            dragStart: { x: 0, y: 0 }
        },

        init: function() {
            this.bindEvents();
            this.initializeDragging();
        },

        bindEvents: function() {
            const self = this;

            $('.ksf-orgchart-search-input').on('input', $.debounce(300, function() {
                self.handleSearch($(this));
            }));

            $('.ksf-orgchart-refresh-btn').on('click', function() {
                self.refreshChart();
            });

            $('.ksf-orgchart-context-btn').on('click', function() {
                self.switchContext($(this));
            });

            $('.ksf-orgchart-levels-input').on('change', function() {
                self.handleLevelChange($(this));
            });

            $('.ksf-orgchart-zoom-btn').on('click', function() {
                self.handleZoom($(this).data('action'));
            });

            $(document).on('click', '.ksf-orgchart-node', function(e) {
                self.handleNodeClick($(this), e);
            });

            $(document).on('dblclick', '.ksf-orgchart-node', function(e) {
                self.handleNodeDblClick($(this), e);
            });
        },

        handleSearch: function($input) {
            const query = $input.val().trim();
            const $results = $input.next('.ksf-orgchart-search-results');

            if (query.length < 2) {
                $results.hide();
                return;
            }

            $.ajax({
                url: KSF_ORGCHART.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ksf_orgchart_search',
                    nonce: $input.data('nonceField') || KSF_ORGCHART.config.nonceField,
                    query: query,
                    context: KSF_ORGCHART.state.context
                },
                success: function(response) {
                    if (response.success && response.data) {
                        KSF_ORGCHART.renderSearchResults($results, response.data);
                    }
                },
                error: function() {
                    $results.html('<div class="search-error">Search failed</div>');
                }
            });
        },

        renderSearchResults: function($container, results) {
            let html = '<div class="search-results-list">';

            if (results.length === 0) {
                html += '<div class="search-no-results">No employees found</div>';
            } else {
                results.slice(0, 10).forEach(function(item) {
                    html += '<div class="search-result-item" data-node-id="' + item.id + '">' +
                        '<span class="result-name">' + item.name + '</span>' +
                        '<span class="result-title">' + item.title + '</span>' +
                        '</div>';
                });
            }

            html += '</div>';
            $container.html(html).show();

            $container.find('.search-result-item').on('click', function() {
                const nodeId = $(this).data('nodeId');
                KSF_ORGCHART.loadNodeCentered(nodeId);
                $container.hide();
            });
        },

        switchContext: function($button) {
            const newContext = $button.data('context');

            $('.ksf-orgchart-context-btn').removeClass('active');
            $button.addClass('active');

            KSF_ORGCHART.state.context = newContext;

            if (newContext === 'project') {
                $('.ksf-orgchart-context-project').show();
                KSF_ORGCHART.loadProjectSelector();
            } else {
                $('.ksf-orgchart-context-project').hide();
                KSF_ORGCHART.refreshChart();
            }
        },

        loadProjectSelector: function() {
            $.ajax({
                url: KSF_ORGCHART.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ksf_orgchart_get_projects',
                    nonce: KSF_ORGCHART.config.nonceField
                },
                success: function(response) {
                    if (response.success && response.data) {
                        KSF_ORGCHART.populateProjectSelector(response.data);
                    }
                }
            });
        },

        populateProjectSelector: function(projects) {
            const $select = $('#ksf-orgchart-project-id');
            $select.find('option:not(:first)').remove();

            projects.forEach(function(proj) {
                $select.append('<option value="' + proj.id + '">' + proj.name + '</option>');
            });

            $select.off('change').on('change', function() {
                KSF_ORGCHART.state.projectId = $(this).val();
                KSF_ORGCHART.refreshChart();
            });
        },

        handleLevelChange: function($input) {
            const $parent = $input.closest('.ksf-orgchart-level-control');
            const isUp = $parent.find('label').text().includes('Up');
            const value = parseInt($input.val(), 10) || 0;

            if (isUp) {
                KSF_ORGCHART.state.levelsUp = Math.min(5, Math.max(0, value));
            } else {
                KSF_ORGCHART.state.levelsDown = Math.min(10, Math.max(0, value));
            }
        },

        refreshChart: function() {
            const data = {
                action: 'ksf_orgchart_load',
                nonce: KSF_ORGCHART.config.nonceField,
                nodeId: KSF_ORGCHART.state.centerNodeId || 0,
                levelsUp: KSF_ORGCHART.state.levelsUp,
                levelsDown: KSF_ORGCHART.state.levelsDown,
                context: KSF_ORGCHART.state.context,
                projectId: KSF_ORGCHART.state.projectId
            };

            $.ajax({
                url: KSF_ORGCHART.config.ajaxUrl,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success && response.data) {
                        KSF_ORGCHART.renderChart(response.data);
                    }
                },
                error: function() {
                    console.error('Failed to load org chart');
                }
            });
        },

        loadNodeCentered: function(nodeId) {
            KSF_ORGCHART.state.centerNodeId = nodeId;
            KSF_ORGCHART.refreshChart();
        },

        renderChart: function(data) {
            const $svg = $('.ksf-orgchart-svg');
            const $nodesGroup = $svg.find('.ksf-orgchart-nodes');
            const $connectorsGroup = $svg.find('.ksf-orgchart-connectors');

            $nodesGroup.empty();
            $connectorsGroup.empty();

            const viewBox = data.viewBox || { width: 800, height: 600 };
            $svg.attr('viewBox', '0 0 ' + viewBox.width + ' ' + viewBox.height);

            data.nodes.forEach(function(node) {
                const pos = self.calculateNodePosition(node, viewBox);
                $nodesGroup.append(KSF_ORGCHART.createNodeElement(node, pos));
            });

            for (let i = 1; i < data.nodes.length; i++) {
                const fromNode = data.nodes[0];
                const toNode = data.nodes[i];
                const fromPos = self.calculateNodePosition(fromNode, viewBox);
                const toPos = self.calculateNodePosition(toNode, viewBox);
                $connectorsGroup.append(KSF_ORGCHART.createConnector(fromPos, toPos));
            }
        },

        calculateNodePosition: function(node, viewBox) {
            const boxWidth = 180;
            const boxHeight = 80;
            const levelSpacing = 100;
            const nodeSpacing = 20;
            const padding = 40;

            const level = node.level || 0;
            const indexInLevel = node.indexInLevel || 0;
            const countInLevel = node.countInLevel || 1;

            const centerOffset = (viewBox.width - boxWidth) / 2;
            const levelOffset = (countInLevel - 1) * (boxWidth + nodeSpacing) / 2;

            const x = padding + centerOffset + levelOffset + indexInLevel * (boxWidth + nodeSpacing);
            const y = padding + level * (boxHeight + levelSpacing);

            return { x: x, y: y };
        },

        createNodeElement: function(node, pos) {
            const nodeId = node.id || 0;
            const name = node.name || 'Unknown';
            const title = node.title || '';
            const department = node.department || '';

            return $('<g/>', {
                'class': 'ksf-orgchart-node',
                'data-node-id': nodeId,
                'data-level': node.level || 0
            }).append([
                $('<rect/>', {
                    'class': 'ksf-orgchart-node-bg',
                    'width': 180,
                    'height': 80,
                    'rx': 4,
                    'x': pos.x,
                    'y': pos.y
                }),
                $('<text/>', {
                    'class': 'ksf-orgchart-node-name',
                    'x': pos.x + 90,
                    'y': pos.y + 25,
                    'text-anchor': 'middle'
                }).text(name.substring(0, 20)),
                $('<text/>', {
                    'class': 'ksf-orgchart-node-title',
                    'x': pos.x + 90,
                    'y': pos.y + 45,
                    'text-anchor': 'middle'
                }).text(title.substring(0, 25)),
                $('<text/>', {
                    'class': 'ksf-orgchart-node-dept',
                    'x': pos.x + 90,
                    'y': pos.y + 60,
                    'text-anchor': 'middle'
                }).text(department.substring(0, 25))
            ]);
        },

        createConnector: function(fromPos, toPos) {
            const x1 = fromPos.x + 90;
            const y1 = fromPos.y + 80;
            const x2 = toPos.x + 90;
            const y2 = toPos.y;

            const midY = (y1 + y2) / 2;

            const pathD = 'M' + x1 + ',' + y1 +
                ' L' + x1 + ',' + midY +
                ' L' + x2 + ',' + midY +
                ' L' + x2 + ',' + y2;

            return $('<path/>', {
                'class': 'ksf-orgchart-connector',
                'd': pathD,
                'fill': 'none',
                'stroke': '#666',
                'stroke-width': 2
            });
        },

        handleNodeClick: function($node, e) {
            if (KSF_ORGCHART.state.isDragging) return;

            const nodeId = $node.data('nodeId');
            const $popup = $('.ksf-orgchart-employee-popup');

            $.ajax({
                url: KSF_ORGCHART.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ksf_orgchart_employee_data',
                    nonce: KSF_ORGCHART.config.nonceField,
                    nodeId: nodeId,
                    context: KSF_ORGCHART.state.context
                },
                success: function(response) {
                    if (response.success && response.data) {
                        KSF_ORGCHART.showEmployeePopup(response.data, e);
                    }
                }
            });
        },

        handleNodeDblClick: function($node, e) {
            const nodeId = $node.data('nodeId');
            KSF_ORGCHART.loadNodeCentered(nodeId);
        },

        showEmployeePopup: function(data, event) {
            const $popup = $('.ksf-orgchart-employee-popup');
            let html = '<h3>' + (data.name || 'Employee') + '</h3>';
            html += '<p><strong>Title:</strong> ' + (data.title || 'N/A') + '</p>';
            html += '<p><strong>Department:</strong> ' + (data.department || 'N/A') + '</p>';

            if (data.email) {
                html += '<p><strong>Email:</strong> ' + data.email + '</p>';
            }
            if (data.phoneExt) {
                html += '<p><strong>Phone:</strong> ' + data.phoneExt + '</p>';
            }

            $popup.find('.popup-content').html(html);
            $popup.show();

            const offset = 20;
            $popup.css({
                left: (event.pageX + offset) + 'px',
                top: (event.pageY + offset) + 'px'
            });

            $popup.find('.ksf-orgchart-popup-close').off('click').on('click', function() {
                $popup.hide();
            });

            $(document).off('click.popup-close').on('click.popup-close', function(e) {
                if (!$popup.is(e.target) && $popup.has(e.target).length === 0) {
                    $popup.hide();
                }
            });
        },

        handleZoom: function(action) {
            const $svg = $('.ksf-orgchart-svg');
            let scale = KSF_ORGCHART.state.scale;
            const minScale = 0.25;
            const maxScale = 1.0;

            switch (action) {
                case 'zoom-in':
                    scale = Math.min(maxScale, scale + 0.1);
                    break;
                case 'zoom-out':
                    scale = Math.max(minScale, scale - 0.1);
                    break;
                case 'zoom-fit':
                    scale = 1.0;
                    break;
            }

            KSF_ORGCHART.state.scale = scale;
            $svg.css('transform', 'scale(' + scale + ')');
        },

        initializeDragging: function() {
            const $svg = $('.ksf-orgchart-svg');
            let startX, startY, originalX, originalY;

            $svg.on('mousedown', function(e) {
                if ($(e.target).closest('.ksf-orgchart-node').length > 0) {
                    return;
                }
                KSF_ORGCHART.state.isDragging = true;
                startX = e.pageX;
                startY = e.pageY;
            });

            $(document).on('mousemove', function(e) {
                if (!KSF_ORGCHART.state.isDragging) return;

                const dx = e.pageX - startX;
                const dy = e.pageY - startY;

                const currentTransform = $svg.css('transform');
                const match = currentTransform.match(/translate\(([-\d.]+),\s*([-\d.]+)\)/);

                let tx = 0, ty = 0;
                if (match) {
                    tx = parseFloat(match[1]);
                    ty = parseFloat(match[2]);
                }

                $svg.css('transform', 'translate(' + (tx + dx) + 'px, ' + (ty + dy) + 'px)');

                startX = e.pageX;
                startY = e.pageY;
            });

            $(document).on('mouseup', function() {
                KSF_ORGCHART.state.isDragging = false;
            });
        }
    };

    $(document).ready(function() {
        if ($('.ksf-orgchart-container').length > 0) {
            KSF_ORGCHART.config = window.ksfOrgChartConfig || {};
            KSF_ORGCHART.init();
        }
    });

    window.KSF_ORGCHART = KSF_ORGCHART;

})(jQuery);