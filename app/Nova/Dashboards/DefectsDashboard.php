<?php

namespace App\Nova\Dashboards;

use App\Nova\Resources\Issue;
use App\Nova\Resources\IssueChangelogItem;

class DefectsDashboard extends Dashboard
{
    /**
     * The displayable name for this dashboard.
     *
     * @var string
     */
    protected static $label = 'Defects';

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            static::getCreatedValueMetric(),
            static::getCountByLabelPartitionMetric(),
            static::getCountByVersionPartitionMetric(),
 
            static::getInflowTrendMetric(),
            static::getOutflowTrendMetric(),
            static::getEquilibriumTrendMetric(),

            static::getActualDelinquenciesTrendMetric(),
            static::getEstimatedDelinquenciesTrendMetric(),
            static::getSatisfactionValueMetric(),

            static::getCountByEpicPartitionMetric(),
            static::getCountByPriorityPartitionMetric(),
            static::getCountByAssigneePartitionMetric()
        ];
    }

    /**
     * Returns the scope for this dashboard.
     *
     * @return \Closure
     */
    public static function scope()
    {
        return function($query) {
            $query->defects();
        };
    }

    /**
     * Returns the created value metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getCreatedValueMetric()
    {
        return Issue::getIssueCreatedByDateValue()
            ->label(static::$label . ' Created')
            ->scope(static::scope())
            ->help('This metric shows the number of ' . static::$label . ' that were recently created.');
    }

    /**
     * Returns the count by label partition metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getCountByLabelPartitionMetric()
    {
        return (new \App\Nova\Metrics\IssueCountPartition)
            ->groupByLabel()
            ->scope(static::scope())
            ->setName(static::$label . ' Rem. Count (by Label)');
    }

    /**
     * Returns the count by version partition metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getCountByVersionPartitionMetric()
    {
        return (new \App\Nova\Metrics\IssueCountPartition)
            ->groupByVersion()
            ->scope(static::scope())
            ->setName(static::$label . ' Rem. Count (by Version)');
    }

    /**
     * Returns the inflow trend metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getInflowTrendMetric()
    {
        return Issue::getIssueCreatedByDateTrend()
            ->label(static::$label . ' Inflow')
            ->scope(static::scope());
    }

    /**
     * Returns the outflow trend metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getOutflowTrendMetric()
    {
        return Issue::getIssueCreatedByDateTrend()
            ->label(static::$label . ' Outflow')
            ->dateColumn('resolution_date')
            ->scope(static::scope());
    }

    /**
     * Returns the equilibrium trend metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getEquilibriumTrendMetric()
    {
        return (new \App\Nova\Metrics\TrendComparisonValue)
            ->label(static::$label . ' Equilibrium')
            ->trends([
                static::getOutflowTrendMetric(),
                static::getInflowTrendMetric()
            ])
            ->format([
                'output' => 'percent',
                'mantissa' => 0
            ])
            ->help('This metric shows the percent-comparison between the inflow and outflow of ' . static::$label . ', where 100% indicates stagnation.');
    }

    /**
     * Returns the actual delinquencies trend metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getActualDelinquenciesTrendMetric()
    {
        return Issue::getIssueDeliquenciesByDueDateTrend()
            ->label(static::$label . ' Act. Delinquencies')
            ->scope(static::scope());
    }

    /**
     * Returns the estimated delinquencies trend metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getEstimatedDelinquenciesTrendMetric()
    {
        return Issue::getIssueDeliquenciesByEstimatedDateTrend()
            ->label(static::$label . ' Est. Delinquencies')
            ->scope(static::scope());
    }

    /**
     * Returns the satisfaction value metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getSatisfactionValueMetric()
    {
        $statuses = [
            'New',
            'In Design',
            'Need Client Clarification',
            'Dev Help Needed',
            'Waiting for approval',
            'Validating',
            'Assigned',
            'Dev Hold',
            'Dev Complete',
            'In Development',
            'Testing Failed',
            'Ready to Test [Test]',
            'Ready to test [UAT]',
            'Test Help Needed'
        ];

        return IssueChangelogItem::getPromiseIntegrityValue($statuses)
            ->label(static::$label . ' Commitments Kept')
            ->scope(static::scope())
            ->help('This metric shows the percentage of ' . static::$label . ' with recent due dates that were completed prior to becoming delinquent.');
    }

    /**
     * Returns the count by epic partition metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getCountByEpicPartitionMetric()
    {
        return (new \App\Nova\Metrics\IssueCountPartition)
            ->groupByEpic()
            ->scope(static::scope())
            ->setName(static::$label . ' Rem. Count (by Epic)');
    }

    /**
     * Returns the count by priority partition metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getCountByPriorityPartitionMetric()
    {
        return (new \App\Nova\Metrics\IssueCountPartition)
            ->groupByPriority()
            ->scope(static::scope())
            ->setName(static::$label . ' Rem. Count (by Priority)');
    }

    /**
     * Returns the count by assignee partition metric for this dashboard.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public static function getCountByAssigneePartitionMetric()
    {
        return (new \App\Nova\Metrics\IssueCountPartition)
            ->groupByAssignee()
            ->scope(static::scope())
            ->setName(static::$label . ' Rem. Count (by Assignee)');
    }
}
