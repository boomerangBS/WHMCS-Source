<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace whmcs\cron\task\ticketscheduledactions\_\traversable;

namespace WHMCS\Cron\Task;

class TicketScheduledActions extends \WHMCS\Scheduling\Task\AbstractTask
{
    protected $defaultPriority = 1720;
    protected $defaultFrequency = 1;
    protected $defaultDescription = "Process Ticket Scheduled Actions";
    protected $defaultName = "Ticket Scheduled Actions";
    protected $systemName = "TicketScheduledActions";
    protected $outputs = [];
    private $pageLimit = 100;
    private $taskEpoch;
    private $maxFailureDuration = 60;
    private $maxInProgressDuration = 60;
    public function setTaskEpoch(\WHMCS\Carbon $dateTime) : \self
    {
        $this->taskEpoch = $dateTime;
        return $this;
    }
    public function setFailureDuration($duration) : \self
    {
        $this->maxFailureDuration = $duration;
        return $this;
    }
    public function setInProgressDuration($duration) : \self
    {
        $this->maxInProgressDuration = $duration;
        return $this;
    }
    public function setPageLimit($pageLimit) : \self
    {
        $this->pageLimit = $pageLimit;
        return $this;
    }
    public function __invoke() : \self
    {
        if(!isset($this->taskEpoch)) {
            $this->taskEpoch = \WHMCS\Carbon::now();
        }
        $this->reclamation();
        $this->processScheduledActions();
        return $this;
    }
    public function reclamation() : void
    {
        $updateAsOf = $this->taskEpoch;
        $duration = $this->maxFailureDuration;
        if($duration === -1) {
            $reviveVsTimeOutDemarcation = \WHMCS\Carbon::createFromTimestamp(1);
        } else {
            $reviveVsTimeOutDemarcation = $this->taskEpoch->clone()->subMinutes($duration);
        }
        $factory = $this->factory();
        $reclamationAgent = $factory->reclamationAgent()->make();
        $reclamationAgent->reviveFailures($reviveVsTimeOutDemarcation, $updateAsOf);
        $defunctInProgressDateTime = $this->taskEpoch->clone()->subMinutes($this->maxInProgressDuration)->setSeconds(0);
        $failedInProgressCount = $reclamationAgent->failLongRunInProgresses($defunctInProgressDateTime, $updateAsOf);
        if(0 < $failedInProgressCount) {
            logActivity(sprintf("The system changed the status for %d scheduled actions to \"Failed\" after they exceeded %d minutes of execution time.", $failedInProgressCount, $this->maxInProgressDuration));
        }
    }
    public function processScheduledActions() : void
    {
        $factory = $this->factory();
        $aggregator = $factory->claimAggregator()->setResponsibilityWindow($this->taskEpoch)->setPageLength($this->pageLimit)->setClaimLockIdentifier(\DI::make("runtime")->processIdentifier())->make();
        $claimProcessingAgent = $factory->claimProcessingAgent()->make();
        foreach ($aggregator as $claim) {
            if(!is_null($claim)) {
                $claimProcessingAgent->handle($claim);
            }
        }
    }
    public function factory() : TicketScheduledActions\_\Factory\Factory
    {
        return new TicketScheduledActions\_\Factory\Factory();
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Factory;

class ClaimAggregatorFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\ClaimLockIdentifier;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\PageLength;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    private $samplerFactory;
    private $claimAgentFactory;
    public function setSamplerFactory(SamplerFactory $samplerFactory) : \self
    {
        $this->samplerFactory = $samplerFactory;
        return $this;
    }
    public function setClaimAgentFactory(ClaimAgentFactory $claimAgentFactory) : \self
    {
        $this->claimAgentFactory = $claimAgentFactory;
        return $this;
    }
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\AggregatingIterator
    {
        $claimAgent = $this->claimAgentFactory->setResponsibilityWindow($this->newestResponsibility())->setClaimLockIdentifier($this->claimLockIdentifier())->make();
        $sampler = $this->samplerFactory->setResponsibilityWindow($this->newestResponsibility())->setPageLength($this->pageLength())->make();
        return (new AggregatingIteratorFactory())->setInnerIterator($sampler)->setAggregator(function (int $ticketId) use($claimAgent) {
            $collection = $claimAgent->claim($ticketId);
            if(is_null($collection)) {
                return NULL;
            }
            return new \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\Claim($collection);
        })->make();
    }
}
class SamplerFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\PageLength;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    protected $innerIteratorFactory;
    protected $outerIteratorFactory;
    protected $iteratorQueryFactory;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\PagingIterator
    {
        $optimisticTicketQueryHandler = $this->iteratorQueryFactory->setResponsibilityWindow($this->newestResponsibility())->make();
        $innerIterator = $this->innerIteratorFactory->setQueryHandler($optimisticTicketQueryHandler)->make();
        return $this->outerIteratorFactory->setInnerIterator($innerIterator)->setPageLength($this->pageLength())->make();
    }
    public function setInnerIteratorFactory(QueryIteratorFactory $innerIteratorFactory) : \self
    {
        $this->innerIteratorFactory = $innerIteratorFactory;
        return $this;
    }
    public function setOuterIteratorFactory(PagingIteratorFactory $outerIteratorFactory) : \self
    {
        $this->outerIteratorFactory = $outerIteratorFactory;
        return $this;
    }
    public function setIteratorQueryFactory(OptimisticTicketQueryFactory $iteratorQueryFactory) : \self
    {
        $this->iteratorQueryFactory = $iteratorQueryFactory;
        return $this;
    }
}
class ClaimAgentFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\ClaimLockIdentifier;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    private $selectQueryFactory;
    private $updateQueryFactory;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Agent\ClaimAgent
    {
        return (new \WHMCS\Cron\Task\TicketScheduledActions\_\Agent\ClaimAgent())->setSelectQuery($this->selectQueryFactory->setClaimLockIdentifier($this->claimLockIdentifier())->setResponsibilityWindow($this->newestResponsibility())->make())->setUpdateQuery($this->updateQueryFactory->setClaimLockIdentifier($this->claimLockIdentifier())->setResponsibilityWindow($this->newestResponsibility())->make());
    }
    public function setSelectQueryFactory(ClaimSelectQueryFactory $factory) : \self
    {
        $this->selectQueryFactory = $factory;
        return $this;
    }
    public function setUpdateQueryFactory(ClaimUpdateQueryFactory $factory) : \self
    {
        $this->updateQueryFactory = $factory;
        return $this;
    }
}
class OptimisticTicketQueryFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Database\OptimisticTicketQueryHandler
    {
        return (new \WHMCS\Cron\Task\TicketScheduledActions\_\Database\OptimisticTicketQueryHandler())->setResponsibilityWindow($this->newestResponsibility());
    }
}
class PagingIteratorFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\PageLength;
    private $innerIterator;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\PagingIterator
    {
        return $this->iterator($this->innerIterator)->setPageLength($this->pageLength());
    }
    public function setInnerIterator(\Iterator $iterator) : \self
    {
        $this->innerIterator = $iterator;
        return $this;
    }
    protected function iterator(\Iterator $innerIterator) : \Iterator
    {
        return new \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\PagingIterator($innerIterator);
    }
}
class ClaimSelectQueryFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\ClaimLockIdentifier;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\TicketOfInterest;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Database\ClaimSelectQueryHandler
    {
        return (new \WHMCS\Cron\Task\TicketScheduledActions\_\Database\ClaimSelectQueryHandler())->setClaimLockIdentifier($this->claimLockIdentifier())->setResponsibilityWindow($this->newestResponsibility());
    }
}
class ClaimUpdateQueryFactory
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\ClaimLockIdentifier;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\TicketOfInterest;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Database\ClaimUpdateQueryHandler
    {
        return (new \WHMCS\Cron\Task\TicketScheduledActions\_\Database\ClaimUpdateQueryHandler())->setClaimLockIdentifier($this->claimLockIdentifier())->setResponsibilityWindow($this->newestResponsibility());
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Database;

class OptimisticTicketQueryHandler
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    protected function baseQuery()
    {
        return \WHMCS\Database\Capsule::table("tblticketactions")->where("status", \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_SCHEDULED)->where("scheduled", "<=", $this->newestResponsibility()->toDateTimeString())->orderBy("scheduled", "asc");
    }
    public function sortSampleQueryResults(\Illuminate\Support\Collection $results) : \Illuminate\Support\Collection
    {
        $uniqueTicketIds = [];
        $i = 0;
        $results->each(function ($row) use($i, $uniqueTicketIds) {
            if(!isset($uniqueTicketIds[$row->ticket_id])) {
                $uniqueTicketIds[$row->ticket_id] = $i++;
            }
        });
        $uniqueIndexedByQuerySort = array_flip($uniqueTicketIds);
        unset($results);
        ksort($uniqueIndexedByQuerySort);
        return collect($uniqueIndexedByQuerySort);
    }
    public function query($offset, int $limit) : \Illuminate\Support\Collection
    {
        return $this->sortSampleQueryResults($this->baseQuery()->limit($limit)->offset($offset)->select(["ticket_id", "scheduled"])->get());
    }
}
class ClaimSelectQueryHandler
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\ClaimLockIdentifier;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\TicketOfInterest;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    public function query() : \Illuminate\Support\Collection
    {
        return \WHMCS\Database\Capsule::table("tblticketactions")->where("ticket_id", $this->ticketId())->where("status", \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_IN_PROGRESS)->where("scheduled", "<=", $this->newestResponsibility()->toDateTimeString())->where("processor_id", $this->claimLockIdentifier())->get();
    }
}
class ClaimUpdateQueryHandler
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\ClaimLockIdentifier;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\TicketOfInterest;
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    public function query() : int
    {
        $pdo = \WHMCS\Database\Capsule::connection()->getPdo();
        $pdoAttributes = [];
        $pdoAttributes[\PDO::ATTR_ERRMODE] = $pdo->getAttribute(\PDO::ATTR_ERRMODE);
        $pdoAttributes[\PDO::ATTR_EMULATE_PREPARES] = $pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        try {
            $stmt = $this->updateStatement($pdo);
            if($stmt->execute()) {
                $stmt->rowCount();
            }
        } finally {
            foreach ($pdoAttributes as $attr => $value) {
                $pdo->setAttribute($attr, $value);
            }
        }
    }
    public function updateStatement(\PDO $pdo) : \PDOStatement
    {
        $queryTemplate = "UPDATE tblticketactions,\n  (\n    SELECT count(*) as count\n    FROM tblticketactions\n    WHERE ticket_id = :ticket_id\n      AND status = :status_in_progress\n  ) as actions_in_progress\nSET\n  status = :status_in_progress,\n  status_at = NOW(),\n  processor_id = :processor_id\nWHERE ticket_id = :ticket_id\n  AND status = :status_scheduled\n  AND scheduled <= :scheduled_ceiling\n  AND actions_in_progress.count = 0;";
        $stmt = $pdo->prepare($queryTemplate);
        $stmt->bindValue("ticket_id", $this->ticketId(), \PDO::PARAM_INT);
        $stmt->bindValue("status_in_progress", \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_IN_PROGRESS, \PDO::PARAM_STR);
        $stmt->bindValue("processor_id", $this->claimLockIdentifier(), \PDO::PARAM_STR);
        $stmt->bindValue("status_scheduled", \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_SCHEDULED, \PDO::PARAM_STR);
        $stmt->bindValue("scheduled_ceiling", $this->newestResponsibility()->toDateTimeString(), \PDO::PARAM_STR);
        return $stmt;
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Traversable;

class QueryIterator implements \Iterator
{
    private $sample;
    private $position = 0;
    private $queryHandler;
    public function hasSample()
    {
        return isset($this->sample);
    }
    public function setQueryHandler(\WHMCS\Cron\Task\TicketScheduledActions\_\Database\OptimisticTicketQueryHandler $queryHandler) : \self
    {
        $this->queryHandler = $queryHandler;
        return $this;
    }
    public function resample($offset, int $limit) : \self
    {
        $this->sample = $this->collectSample($offset, $limit);
        return $this;
    }
    protected function collectSample($offset, int $limit) : \Illuminate\Support\Collection
    {
        return $this->queryHandler->query($offset, $limit);
    }
    public function current()
    {
        return $this->sample->get($this->position);
    }
    public function next()
    {
        $this->position++;
    }
    public function key()
    {
        return $this->position;
    }
    public function valid()
    {
        return $this->sample->has($this->position);
    }
    public function rewind()
    {
        $this->position = 0;
    }
}
class PagingIterator extends \IteratorIterator
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\PageLength;
    protected $sampleOffset = 0;
    protected $rewindCount = 0;
    protected $governedRewindCount = 0;
    protected $maxRewindEffort = 12;
    protected $pageAdvanceCount = 0;
    public function getInnerIterator() : \Iterator
    {
        $iterator = parent::getInnerIterator();
        if(!$iterator->hasSample()) {
            $iterator->resample($this->sampleOffset, $this->pageLength())->rewind();
        }
        return $iterator;
    }
    public function next()
    {
        $innerIterator = $this->getInnerIterator();
        $innerIterator->next();
        if(!$innerIterator->valid()) {
            $this->pageForward($innerIterator);
            if(!$innerIterator->valid()) {
                $this->governedRewind();
            }
        }
    }
    public function decrementOffset($decrementOffsetCount)
    {
        $this->sampleOffset -= $decrementOffsetCount;
    }
    protected function advanceOffset() : void
    {
        $this->sampleOffset += $this->pageLength();
        if($this->sampleOffset < 0) {
            $this->sampleOffset = 0;
        }
    }
    protected function pageForward() : void
    {
        $this->pageAdvanceCount++;
        $this->advanceOffset();
        $this->sample();
        $this->resetOffset();
    }
    protected function resetOffset() : void
    {
        $this->sampleOffset = 0;
    }
    public function sample() : void
    {
        $this->getInnerIterator()->resample($this->sampleOffset, $this->pageLength())->rewind();
    }
    public function governedRewind() : void
    {
        if($this->hasReachedMaxEffort()) {
            return NULL;
        }
        $this->delayEffort($this->governedRewindCount);
        $this->governedRewindCount++;
        $this->rewind();
    }
    protected function hasReachedMaxEffort()
    {
        return $this->governedRewindCount == $this->maxRewindEffort;
    }
    protected function delayEffort($currentIteration) : void
    {
        $index = $currentIteration;
        $delayMap = [0, 0, 0, 1, 1, 2, 3, 5, 8, 13, 21, 34];
        $timeToDelay = $delayMap[$index] ?? $delayMap[count($delayMap) - 1];
        if(0 < $timeToDelay) {
            sleep($timeToDelay);
        }
    }
    public function rewind()
    {
        $this->rewindCount++;
        $this->resetOffset();
        $this->sample();
    }
    public function valid()
    {
        if($this->hasReachedMaxEffort()) {
            return false;
        }
        return $this->getInnerIterator()->valid();
    }
    public function current()
    {
        return $this->getInnerIterator()->current();
    }
    public function key()
    {
        return $this->getInnerIterator()->key();
    }
    public function setSampleOffset($offset) : \self
    {
        $this->sampleOffset = $offset;
        return $this;
    }
    public function getRewindCount() : int
    {
        return $this->rewindCount;
    }
    public function getPageAdvanceCount() : int
    {
        return $this->pageAdvanceCount;
    }
}
class AggregatingIterator extends \IteratorIterator
{
    private $aggregator;
    public function setAggregator(\Closure $aggregator) : \self
    {
        $this->aggregator = $aggregator;
        return $this;
    }
    public function assembleAggregate($data) : Claim
    {
        $aggregator = $this->aggregator;
        return $aggregator($data);
    }
    public function current() : Claim
    {
        $innerIterator = $this->getInnerIterator();
        $current = $innerIterator->current();
        if(is_null($current)) {
            return NULL;
        }
        $aggregate = $this->assembleAggregate($current);
        if(is_null($aggregate)) {
            return NULL;
        }
        $innerIterator->decrementOffset(count($aggregate));
        return $aggregate;
    }
}
class Claim extends \Illuminate\Support\Collection
{
    public function __construct($data)
    {
        $model = new \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction();
        $data = $data instanceof \Illuminate\Support\Collection ? $data->toArray() : $data;
        array_walk($data, function (&$value) use($model) {
            if($value instanceof \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction) {
                return NULL;
            }
            $value = $model->newFromBuilder($value);
        });
        parent::__construct($data);
    }
    public function groupByMinuteResolution() : \Illuminate\Support\Collection
    {
        return $this->collect()->groupBy(function (\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction $action) {
            return $action->scheduled->second(0)->toDateTimeString();
        })->map(function ($actionsOfMinute) {
            return new Claim($actionsOfMinute);
        });
    }
    public function sortByActionPriority() : \self
    {
        return $this->sort([new \WHMCS\Support\Ticket\Actions\ActionsList(), "compareTicketActionPriority"]);
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Agent;

class ClaimAgent
{
    use \WHMCS\Cron\Task\TicketScheduledActions\_\Traits\WindowOfResponsibility;
    private $updateQuery;
    private $selectQuery;
    public function setUpdateQuery(\WHMCS\Cron\Task\TicketScheduledActions\_\Database\ClaimUpdateQueryHandler $updateQuery) : \self
    {
        $this->updateQuery = $updateQuery;
        return $this;
    }
    public function setSelectQuery(\WHMCS\Cron\Task\TicketScheduledActions\_\Database\ClaimSelectQueryHandler $selectQuery) : \self
    {
        $this->selectQuery = $selectQuery;
        return $this;
    }
    public function claim($ticketId) : \Illuminate\Support\Collection
    {
        if($this->claimInDatabase($ticketId)) {
            return $this->getClaimed($ticketId);
        }
        return NULL;
    }
    private function claimInDatabase($ticketId) : int
    {
        $affectedRows = $this->updateQuery->setTicketId($ticketId)->query();
        return 0 < $affectedRows;
    }
    private function getClaimed($ticketId) : \Illuminate\Support\Collection
    {
        return $this->selectQuery->setTicketId($ticketId)->query();
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Traits;

trait WindowOfResponsibility
{
    private $newestResponsibility;
    public function setResponsibilityWindow(\WHMCS\Carbon $dateOfNewestResponsibility) : \self
    {
        $this->newestResponsibility = $dateOfNewestResponsibility->clone()->setSeconds(59);
        return $this;
    }
    public function newestResponsibility() : \WHMCS\Carbon
    {
        return $this->newestResponsibility;
    }
}
trait ClaimLockIdentifier
{
    private $claimLockIdentifier;
    public function setClaimLockIdentifier($identifier) : \self
    {
        $this->claimLockIdentifier = $identifier;
        return $this;
    }
    public function claimLockIdentifier()
    {
        return $this->claimLockIdentifier;
    }
}
trait TicketOfInterest
{
    private $ticketId;
    public function setTicketId($ticketId) : \self
    {
        $this->ticketId = $ticketId;
        return $this;
    }
    public function ticketId() : int
    {
        return $this->ticketId;
    }
}
trait PageLength
{
    private $pageLength = 100;
    public function setPageLength($limit) : \self
    {
        $this->pageLength = $limit;
        return $this;
    }
    public function pageLength() : int
    {
        return $this->pageLength;
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Factory;

class Factory
{
    public function claimAggregator() : ClaimAggregatorFactory
    {
        return (new ClaimAggregatorFactory())->setClaimAgentFactory($this->claimAgent())->setSamplerFactory($this->sampler());
    }
    public function sampler() : SamplerFactory
    {
        return (new SamplerFactory())->setInnerIteratorFactory(new QueryIteratorFactory())->setOuterIteratorFactory(new PagingIteratorFactory())->setIteratorQueryFactory(new OptimisticTicketQueryFactory());
    }
    public function claimAgent() : ClaimAgentFactory
    {
        return (new ClaimAgentFactory())->setSelectQueryFactory(new ClaimSelectQueryFactory())->setUpdateQueryFactory(new ClaimUpdateQueryFactory());
    }
    public function claimProcessingAgent() : ClaimProcessingAgentFactory
    {
        return new ClaimProcessingAgentFactory();
    }
    public function reclamationAgent() : ReclamationAgentFactory
    {
        return new ReclamationAgentFactory();
    }
}
class ReclamationAgentFactory
{
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Agent\ReclamationAgent
    {
        return new \WHMCS\Cron\Task\TicketScheduledActions\_\Agent\ReclamationAgent();
    }
}
class ClaimProcessingAgentFactory
{
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Agent\ClaimProcessingAgent
    {
        return new \WHMCS\Cron\Task\TicketScheduledActions\_\Agent\ClaimProcessingAgent();
    }
}
class QueryIteratorFactory
{
    private $queryHandler;
    public function setQueryHandler(\WHMCS\Cron\Task\TicketScheduledActions\_\Database\OptimisticTicketQueryHandler $queryHandler) : \self
    {
        $this->queryHandler = $queryHandler;
        return $this;
    }
    public function make() : \Iterator
    {
        return $this->iterator()->setQueryHandler($this->queryHandler);
    }
    protected function iterator() : \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\QueryIterator
    {
        return new \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\QueryIterator();
    }
}
class AggregatingIteratorFactory
{
    private $innerIterator;
    private $aggregator;
    public function make() : \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\AggregatingIterator
    {
        return (new \WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\AggregatingIterator($this->innerIterator))->setAggregator($this->aggregator);
    }
    public function setInnerIterator(\Iterator $iterator) : \self
    {
        $this->innerIterator = $iterator;
        return $this;
    }
    public function setAggregator(\Closure $aggregator) : \self
    {
        $this->aggregator = $aggregator;
        return $this;
    }
}
namespace WHMCS\Cron\Task\TicketScheduledActions\_\Agent;

class ClaimProcessingAgent
{
    public function handle(\WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\Claim $claim)
    {
        $claim->groupByMinuteResolution()->each(function (\WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\Claim $minuteResolutionClaim) {
            $this->processClaim($minuteResolutionClaim->sortByActionPriority());
        });
    }
    public function processClaim(\WHMCS\Cron\Task\TicketScheduledActions\_\Traversable\Claim $claim) : void
    {
        foreach ($claim as $scheduledAction) {
            try {
                $action = $scheduledAction->getAction();
                if(!$action->execute()) {
                    throw new \RuntimeException();
                }
                $scheduledAction->complete();
            } catch (\Throwable $e) {
                $scheduledAction->fail();
                \WHMCS\Support\Ticket\ScheduledActions\Logger::factory($scheduledAction)->activityFailure($e->getMessage());
            } finally {
                $scheduledAction->processorId = "";
                $scheduledAction->save();
            }
        }
    }
}
class ReclamationAgent
{
    public function reviveFailures(\WHMCS\Carbon $reviveAsOfOrNewer, \WHMCS\Carbon $markStatusUpdateAsOf)
    {
        $reviveAsOfOrNewer = $reviveAsOfOrNewer->clone()->setSeconds(0);
        \WHMCS\Database\Capsule::table("tblticketactions")->whereIn("status", [\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_FAILED])->where("scheduled", ">=", $reviveAsOfOrNewer->toDateTimeString())->update(["status" => \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_SCHEDULED, "status_at" => $markStatusUpdateAsOf->toDateTimeString()]);
    }
    public function failLongRunInProgresses(\WHMCS\Carbon $olderThan, $newStatusAt) : int
    {
        return \WHMCS\Database\Capsule::table("tblticketactions")->whereIn("status", [\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_IN_PROGRESS])->where("status_at", "<", $olderThan->toDateTimeString())->update(["status" => \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_FAILED, "status_at" => $newStatusAt->toDateTimeString()]);
    }
}

?>