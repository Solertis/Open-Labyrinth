<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_nodes table in database 
 */
class Model_Leap_Map_Node extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'text' => new DB_ORM_Field_String($this, array(
                'max_length' => 4000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'probability' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'conditional' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'conditional_message' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'info' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'link_style_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'link_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'priority_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'kfp' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'undo' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'end' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'x' => new DB_ORM_Field_Double($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'y' => new DB_ORM_Field_Double($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'rgb' => new DB_ORM_Field_String($this, array(
                'max_length' => 8,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),

            'show_info' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'is_private' => new DB_ORM_Field_Boolean($this, array(
                'savable' => TRUE,
                'nullable' => FALSE,
                'default' => FALSE
            )),

            'annotation' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_type',
            )),
            
            'link_style' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('link_style_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_link_style',
            )),
            
            'link_type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('link_type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_link_type',
            )),
            
            'priority' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('priority_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_priority',
            )),
            
            'sections' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id'),
                'child_model' => 'map_node_section_node',
                'parent_key' => array('id'),
            )),
            
            'links' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id_1'),
                'child_model' => 'map_node_link',
                'parent_key' => array('id'),
            )),
            
            'counters' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id'),
                'child_model' => 'map_node_counter',
                'parent_key' => array('id'),
            )),

            'notes' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id'),
                'child_model' => 'dtopic',
                'parent_key' => array('id'),
                'options' => array(
                    array('where', array('status', '=', '1'))
                )
            ))
        );
        self::initialize_metadata($this);
    }

    private static function initialize_metadata($object)
    {
        $metadata = Model_Leap_Metadata::getMetadataRelations("map_node", $object);
        $object->relations = array_merge($object->relations, $metadata);
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_nodes';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getNodesByMap ($mapId, $orderBy = null, $logicSort = null, $lengthSort = false)
    {
        $builder = DB_SQL::select('default')->from($this->table(), 't')->column('t.id', 'id')->where('map_id', '=', $mapId);
        switch ($orderBy)
        {
            case 1:
                $builder = $builder->order_by('id', 'ASC');
                break;
            case 2:
                $builder = $builder->order_by('id', 'DESC');
                break;
            case 3:
                $builder = $builder->order_by('title', 'ASC');
                break;
            case 4:
                $builder = $builder->order_by('title', 'DESC');
                break;
            case 5:
                $builder = $builder->order_by('x', 'ASC');
                break;
            case 6:
                $builder = $builder->order_by('x', 'DESC');
                break;
            case 7:
                $builder = $builder->order_by('y', 'ASC');
                break;
            case 8:
                $builder = $builder->order_by('y', 'DESC');
                break;
            case 9:
                $builder = $builder->join('left', 'map_node_section_nodes', 'r')->on('r.node_id', '=', 't.id')->order_by('r.id', 'ASC');
                break;
            case 10:
                $builder = $builder->join('left', 'map_node_section_nodes', 'r')->on('r.node_id', '=', 't.id')->order_by('r.id', 'DESC');
                break;
            default:
                $builder = $builder->order_by('id', 'ASC');
        }
        $result = $builder->query();
        
        if ($result->is_loaded())
        {
            $nodes = array();
            $rootNodes = array();
            $endNodes = array();

            foreach ($result as $record)
            {
                $tmp = DB_ORM::model('map_node', array((int)$record['id']));
                if ($logicSort != null AND $logicSort == 1)
                {
                    if ($tmp->type_id == 1) { $rootNodes[] = $tmp; }
                    elseif ($tmp->end) { $endNodes[] = $tmp; }
                    else { $nodes[] = $tmp; }
                }
                else $nodes[] = $tmp;
            }
            
            $nodes = array_merge($rootNodes, $nodes);
            $nodes = array_merge($nodes, $endNodes);

            if ($lengthSort) usort($nodes, function($a, $b) { return strlen($b->title) - strlen($a->title); });

            return $nodes;
        }
        return array();
    }
    
    public function getAllNode($mapId = null) {
        $builder = DB_SQL::select('default')->from($this->table());
        if($mapId != null)
            $builder = $builder->where ('map_id', '=', $mapId);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $nodes = array();
            foreach($result as $record) {
                $nodes [] = DB_ORM::model('map_node', array((int)$record['id']));
            }
            
            return $nodes;
        }
        
        return NULL;
    }
    
    public function createNode($values) {
        $mapId = Arr::get($values, 'map_id', NULL);
        if($mapId != NULL) {
            return DB_ORM::model('map_node', array(DB_ORM::insert('map_node')
                ->column('map_id',           $mapId)
                ->column('title',            Arr::get($values, 'mnodetitle', ''))
                ->column('text',             Arr::get($values, 'mnodetext', ''))
                ->column('type_id',          Arr::get($values, 'type_id', FALSE) ? Arr::get($values, 'type_id', FALSE) : 2)
                ->column('is_private',       Arr::get($values, 'is_private', FALSE) ? 1 : 0)
                ->column('probability',      Arr::get($values, 'mnodeprobability', FALSE))
                ->column('info',             Arr::get($values, 'mnodeinfo', ''))
                ->column('link_style_id',    Arr::get($values, 'linkstyle', 1))
                ->column('link_type_id',     2)
                ->column('priority_id',      Arr::get($values, 'priority', 1))
                ->column('undo',             Arr::get($values, 'mnodeUndo', FALSE))
                ->column('end',              (int) Arr::get($values, 'ender', FALSE))
                ->column('show_info',        Arr::get($values, 'show_info', FALSE) ? 1 : 0)
                ->column('annotation',       Arr::get($values, 'annotation', null))
                ->execute()));
        }
        return NULL;
    }

    public function createFullNode ($mapId, $values)
    {
        // if mapId doesn't exist end function
        if ( ! $mapId) return NULL;

        $record = new $this;
        $record->map_id               = $mapId;
        $record->title                = Arr::get($values, 'title', '');
        $record->text                 = Arr::get($values, 'text', '');
        $record->type_id              = Arr::get($values, 'type_id', FALSE);
        $record->probability          = Arr::get($values, 'probability', FALSE);
        $record->conditional          = Arr::get($values, 'conditional', '');
        $record->conditional_message  = Arr::get($values, 'conditional_message', '');
        $record->info                 = Arr::get($values, 'info', '');
        $record->is_private           = Arr::get($values, 'is_private', FALSE) ? 1 : 0;;
        $record->link_style_id        = Arr::get($values, 'link_style_id', 1);
        $record->link_type_id         = Arr::get($values, 'link_type_id', 1);
        $record->priority_id          = Arr::get($values, 'priority_id', 1);
        $record->kfp                  = Arr::get($values, 'kfp', 0);
        $record->undo                 = Arr::get($values, 'undo', FALSE);
        $record->end                  = Arr::get($values, 'end', FALSE);
        $record->x                    = Arr::get($values, 'x', FALSE);
        $record->y                    = Arr::get($values, 'y', FALSE);
        $record->rgb                  = Arr::get($values, 'rgb', FALSE);
        $record->show_info            = Arr::get($values, 'show_info', FALSE) ? 1 : 0;
        $record->annotation           = Arr::get($values, 'annotation', '');
        $record->save();

        return $record->getLastAddedNode($mapId);

    }

    public function createNodeFromJSON($mapId, $values) {
        if($mapId == null) return null;

        $builder = DB_ORM::insert('map_node')
                ->column('map_id', $mapId)
                ->column('title', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'title', '')))))
                ->column('text', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'content', '')))))
                ->column('info', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'support', '')))))
                ->column('is_private', (int) Arr::get($values, 'is_private', 0) ? 1 : 0)
                ->column('probability', (Arr::get($values, 'isExit', 'false') == 'true'))
                ->column('type_id', (Arr::get($values, 'isRoot', 'false') == 'true') ? 1 : 2)
                ->column('link_style_id', Arr::get($values, 'linkStyle', 1))
                ->column('priority_id', Arr::get($values, 'nodePriority', 1))
                ->column('undo', (Arr::get($values, 'undo', 'false') == 'true'))
                ->column('end', (Arr::get($values, 'isEnd', 'false') == 'true'))
                ->column('x', Arr::get($values, 'x', 0))
                ->column('y', Arr::get($values, 'y', 0))
                ->column('rgb', Arr::get($values, 'color', '#FFFFFF'))
                ->column('annotation', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'annotation', null)))))
                ->column('show_info', (int) Arr::get($values, 'showInfo', 0));

        return $builder->execute();
    }

    public function updateNodeFromJSON($mapId, $nodeId, $values) {
        $this->id = $nodeId;
        $this->load();
        if($this) {
            $this->title = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'title', ''))));
            $text = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'content', ''))));
            $info = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'support', ''))));
            $nodetext = $this->checkReference($mapId, $nodeId, $text, $info);
            if(isset($nodetext['text'])){
                $this->text = $nodetext['text'];
            }else{
                $this->text = $text;
            }
            if(isset($nodetext['info'])){
                $this->info = $nodetext['info'];
            }else {
                $this->info = $info;
            }
            $reference = DB_ORM::model('map_node_reference')->getNotParent($mapId, $nodeId, 'INFO');
            $privete = (int) Arr::get($values, 'isPrivate', 0);
            if($reference != NULL && $privete){
                $this->is_private = FALSE;
            } else {
                $this->is_private = $privete;
            }
            $this->probability = Arr::get($values, 'isExit', 'false') == 'true';
            $this->type_id = (Arr::get($values, 'isRoot', 'false') == 'true') ? 1 : 2;
            $this->link_style_id = Arr::get($values, 'linkStyle', 1);
            $this->priority_id = Arr::get($values, 'nodePriority', 1);
            $this->undo = Arr::get($values, 'undo', 'false') == 'true';
            $this->end = Arr::get($values, 'isEnd', 'false') == 'true';
            $this->x = Arr::get($values, 'x', 0);
            $this->y = Arr::get($values, 'y', 0);
            $this->rgb = Arr::get($values, 'color', '#FFFFFF');
            $this->show_info = (int) Arr::get($values, 'showInfo', 0);
            $this->annotation = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'annotation', null))));

            $this->save();
        }
    }

    public function updateNodeStyle($nodeId, $values) {
        $this->id = $nodeId;
        $this->load();

        if($this->is_loaded()) {
            $this->x   = Arr::get($values, 'x', 0);
            $this->y   = Arr::get($values, 'y', 0);
            $this->rgb = Arr::get($values, 'rgb', '#FFFFFF');

            $this->save();
        }
    }

    public function updateNodeText($nodeId, $text) {
        $this->id = $nodeId;
        $this->load();

        if($this->is_loaded()) {
            $this->text = $text;

            $this->save();
        }
    }

    public function updateNodeInfo($nodeId, $text) {
        $this->id = $nodeId;
        $this->load();

        if($this->is_loaded()) {
            $this->info = $text;
            $this->save();
        }
    }

    public function getLastAddedNode($mapId)
    {
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->query();

        return $result->is_loaded() ? DB_ORM::model('map_node', array($result[0]['id'])) : NULL;
    }

    public function createDefaultRootNode($mapId) {
        if($mapId != NULL) {
            $this->map_id = $mapId;
            $this->title = 'Root Node';
            $this->text = '';
            $this->info = '';
            $this->is_private = FALSE;
            $this->probability = FALSE;
            $this->link_style_id = 1;
            $this->link_type_id = 2;
            $this->priority_id = 1;
            $this->type_id = 1;
            $this->undo = FALSE;
            $this->end = FALSE;
            $this->show_info = FALSE;

            $this->save();
        }
    }
    
    public function updateNode($nodeId, $values) {
        $this->id = $nodeId;
        $this->load();
        if($this) {
            $this->title = Arr::get($values, 'mnodetitle', $this->title);
            $this->text = Arr::get($values, 'mnodetext', $this->text);
            $this->info = Arr::get($values, 'mnodeinfo', $this->info);
            $this->is_private = Arr::get($values, 'is_private', FALSE) ? 1 : 0;
            $this->probability = Arr::get($values, 'mnodeprobability', $this->probability);
            $this->link_style_id = Arr::get($values, 'linkstyle', $this->link_style_id);
            $this->link_type_id = Arr::get($values, 'linktype', $this->link_type_id);
            $this->priority_id = Arr::get($values, 'priority', $this->priority_id);
            $this->undo = Arr::get($values, 'mnodeUndo', $this->undo);
            $this->end = Arr::get($values, 'ender', $this->end);
            $this->show_info = Arr::get($values, 'show_info', FALSE) ? 1 : 0;
            $this->annotation = Arr::get($values, 'annotation', null);

            $this->save();
            
            return $this;
        }
        
        return NULL;
    }
    
    public function getRootNode() {
        $typeId = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        if($typeId != NULL) {
            $builder = DB_SQL::select('default')->from($this->table())->where('type_id', '=', $typeId);
            $result = $builder->query();

            if($result->is_loaded()) {
                return DB_ORM::model('map_node', array((int)$result[0]['id']));
            }
        }
        
        return NULL;
    }
    
    public function setRootNode($mapId, $nodeId) {
        $rootNode = $this->getRootNodeByMap($mapId);
        if($rootNode != NULL) {
            $rootNode->type_id = DB_ORM::model('map_node_type')->getTypeByName('child')->id;
            $rootNode->save();
        }
        
        $this->id = $nodeId;
        $this->load();
        $this->type_id = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        $this->save();
    }
    
    public function addCondtional($nodeId, $values, $countOfConditional) {
        $this->id = $nodeId;
        $this->load();
        
        if($this) {
            $this->conditional_message = Arr::get($values, 'abs', '');
            $operator = Arr::get($values, 'operator', 'and');
            $conditional = '(';
            for($i = 0; $i < $countOfConditional - 1; $i++) {
                $conditional .= '['.Arr::get($values, 'el_'.$i, 0).']'.$operator;
            }
            
            if($countOfConditional > 0) {
                $conditional .=  '['.Arr::get($values, 'el_'.($countOfConditional - 1), 0).']';
            }
            $conditional .= ')';
            
            $this->conditional = $conditional;
            
            $this->save();
        }
    }
    
    public function updateAllNode($values, $mapId = null) {
        $nodes = $this->getAllNode($mapId);
        foreach($nodes as $node) {
            $node->title = Arr::get($values, 'title_'.$node->id, $node->title);
            $node->text = Arr::get($values, 'text_'.$node->id, $node->text);
            $node->info = Arr::get($values, 'info_'.$node->id, $node->info);
            
            $node->save();
        }
    }
    
    public function getAllNodesNotInSection($mapId = null) {
        $tableName = DB_ORM::model('map_node_section_node');
        $builder = DB_SQL::select('default')
                ->from($tableName::table())
                ->column('node_id');

        $allNodeInSectionresult = $builder->query();
        
        $ids = array();
        if($allNodeInSectionresult->is_loaded()) {
            foreach($allNodeInSectionresult as $record) {
                $ids[] = (int)$record['node_id'];
            }
        }
        $builder = NULL;
        if(count($ids) > 0) {
            $builder = DB_SQL::select('default')->from($this->table())->where('id', 'NOT IN', $ids);
        } else {
            $builder = DB_SQL::select('default')->from($this->table());
        }
        if(isset($mapId))$builder->where("map_id","=",$mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $nodes = array();
            foreach($result as $record) {
                $nodes[] = DB_ORM::model('map_node', array((int)$record['id']));
            }
            
            return $nodes;
        }
        
        return NULL;
    }

    public function setLinkStyle($linkStyleId) {
        $linkStyles = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
        if($linkStyles != null && count($linkStyles) > 0) {
            foreach($linkStyles as $style) {
                if($style->id == $linkStyleId) {
                    DB_ORM::update('map_node')->set('link_style_id', $linkStyleId)->execute();
                    break;
                }
            }
        }
    }

    public function getNodesWithoutLink($nodeId) {
        $this->id = $nodeId;
        $this->load();
        
        if(count($this->links) > 0) {
            $ids = array();
            foreach($this->links as $link) {
                $ids[] = $link->node_2->id;
            }
            
            $builder = DB_SQL::select('default')
                    ->from($this->table())
					->where('map_id', '=', $this->map_id, 'AND')
                    ->where('id', 'NOT IN', $ids);
            $result = $builder->query();
            
            if($result->is_loaded()) {
                $nodes = array();
                foreach($result as $record) {
                    $nodes[] = DB_ORM::model('map_node', array((int)$record['id']));
                }
                
                return $nodes;
            }
            
            return NULL;
        } 
            
        return $this->getNodesByMap($this->map_id);
    }
    
    public function getCounter($counterId) {
        if(count($this->counters) > 0) {
            foreach($this->counters as $counter) {
                if($counter->counter_id == $counterId) {
                    return $counter;
                }
            }
            return NULL;
        }
        return NULL;
    }
    
    public function getNodeById($nodeId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $nodeId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return DB_ORM::model('map_node', array((int)$result[0]['id']));
        }
        
        return NULL;
    }
    
    public function getRootNodeByMap($mapId) {
        $typeId = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        if($typeId != NULL) {
            $builder = DB_SQL::select('default')
                    ->from($this->table())
                    ->where('type_id', '=', $typeId, 'AND')
                    ->where('map_id', '=', $mapId);
            $result = $builder->query();

            if($result->is_loaded()) {
                return DB_ORM::model('map_node', array((int)$result[0]['id']));
            }
        }
        
        return NULL;
    }
    
    public function deleteNode($nodeId) {
        $this->id = $nodeId;
        $this->load();
        
        if($this->is_loaded()) {
            if(count($this->links) > 0) {
                foreach($this->links as $link) {
                    $link->delete();
                }
            }
        }
        
        $this->delete();
    }
    
    public function createVUENode($mapId, $title, $text, $x, $y, $rgb) {
        $this->map_id = $mapId;
        $this->title = $title;
        $this->text = $text;
        $this->type_id = 2;
        $this->x = $x;
        $this->y = $y;
        $this->rgb = $rgb;
        $this->info = '';
        $this->probability = FALSE;
        $this->link_style_id = 1;
        $this->priority_id = 1;
        $this->undo = FALSE;
        $this->end = FALSE;
        
        $this->save();
    }
    
    public function duplicateNodes ($fromMapId, $toMapId)
    {
        $nodesMap = array();
        foreach($this->getNodesByMap($fromMapId) as $node)
        {
            $newNode = $this->createFullNode($toMapId, array(
                'title'                 => $node->title,
                'text'                  => $node->text,
                'type_id'               => $node->type_id,
                'probability'           => $node->probability,
                'conditional'           => $node->conditional,
                'conditional_message'   => $node->conditional_message,
                'info'                  => $node->info,
                'is_private'            => $node->is_private,
                'link_style_id'         => $node->link_style_id,
                'link_type_id'          => $node->link_type_id,
                'priority_id'           => $node->priority_id,
                'kfp'                   => $node->kfp,
                'undo'                  => $node->undo,
                'end'                   => $node->end,
                'x'                     => $node->x,
                'y'                     => $node->y,
                'rgb'                   => $node->rgb,
                'show_info'             => $node->show_info,
                'annotation'            => $node->annotation,
            ));
            $nodesMap[$node->id] = $newNode->id;
        }
        return $nodesMap;
    }
    
    public function replaceDuplcateNodeContenxt($nodeMap, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap)
    {
        foreach ($nodeMap as $v)
        {
            $this->id = $v;
            $this->load();
            $this->text = $this->parseText($this->text, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap);
            $this->info = $this->parseText($this->info, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap);
            $this->save();
        }
    }
    
    private function parseText($text, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap) {
        $result = $text;

        $codes = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD', 'QU', 'INFO');

        foreach ($codes as $code) {
            $regExp = '/[\[' . $code . ':\d\]]+/';
            if (preg_match_all($regExp, $text, $matches)) {
                foreach ($matches as $match) {
                    foreach ($match as $value) {
                        if (stristr($value, '[[' . $code . ':')) {
                            $m = explode(':', $value);
                            $id = substr($m[1], 0, strlen($m[1]) - 2);
                            if (is_numeric($id)) {
                                $replaceString = '';
                                switch ($code) {
                                    case 'MR':
                                        if(isset($elemMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $elemMap[(int)$id] . ']]';
                                        break;
                                    case 'AV':
                                        if(isset($avatarMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $avatarMap[(int)$id] . ']]';
                                        break;
                                    case 'CHAT':
                                        if(isset($chatMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $chatMap[(int)$id] . ']]';
                                        break;
                                    case 'QU':
                                        if(isset($questionMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $questionMap[(int)$id] . ']]';
                                        break;
                                    case 'VPD':
                                        if(isset($vpdMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $vpdMap[(int)$id] . ']]';
                                        break;
                                    case 'DAM':
                                        if(isset($damMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $damMap[(int)$id] . ']]';
                                        break;
                                    case 'INFO':
                                        break;
                                }

                                $result = str_replace('[[' . $code . ':' . $id . ']]', $replaceString, $result);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $nodes = array();
            foreach($result as $record) {
                $nodes[] = $record;
            }

            return $nodes;
        }

        return NULL;
    }

    public function getEndNodesForMap($mapId) {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->where('map_id', '=', $mapId, 'AND')
                           ->where('end', '=', 1)
                           ->column('id')
                           ->query();

        $result = null;
        if($records->is_loaded()) {
            $result = array();
            foreach($records as $record) {
                $result[] = DB_ORM::model('map_node', array((int)$record['id']));
            }
        }

        return $result;
    }

    public function getNodeName($nodeId) {
        $this->id = $nodeId;
        $this->load();

        $name = null;

        if ($this->is_loaded()){
            $name = $this->title;
        }

        return $name;
    }

    private function checkReference($mapId, $nodeId, $text, $info){
        $textRecords = array();
        $textRecords[] = $this->getReferenceField($mapId, $nodeId, $text);
        $textRecords[] = $this->getReferenceField($mapId, $nodeId, $info);
        $replace = array();
        for($i = 0; $i<=1; $i++){
            if($textRecords[$i]){
                foreach($textRecords[$i] as $record){
                    $key = false;
                    $parent = false;
                    switch ($record['type']){
                        case 'MR':
                            $element = DB_ORM::model('map_element', array((int) $record['element_id']));
                            if($element->map_id && !$element->is_private){
                                $key = true;
                            }
                            if($element->map_id == $mapId){
                                $parent = true;
                            }
                            break;
                        case 'VPD':
                            $vpdElements = DB_ORM::model('map_vpd_element')->getValuesByVpdId($record['element_id']);
                            if($vpdElements){
                                foreach($vpdElements as $vpdElement){
                                    if($vpdElement->key == 'Private'){
                                        $vpdPrivate = $vpdElement->value;
                                    }
                                }
                            }
                            if(empty($vpdPrivate)) {
                                $vpdPrivate = 'Off';
                            }
                            if($vpdPrivate == 'Off'){
                                $key = true;
                            }
                            $vpdMapId = DB_ORM::model('map_vpd')->getMapId($record['element_id']);
                            if($vpdMapId == $mapId){
                                $parent = true;
                            }
                            break;
                        case 'QU':
                            $question = DB_ORM::model('map_question', array((int) $record['element_id']));
                            if($question->map_id && !$question->is_private){
                                $key = true;
                            }
                            if($question->map_id == $mapId){
                                $parent = true;
                            }
                            break;
                        case 'INFO':
                            $nod = DB_ORM::model('map_node', array((int) $record['element_id']));
                            if($nod->map_id && !$nod->is_private){
                                $key = true;
                            }
                            if($nod->map_id == $mapId){
                                $parent = true;
                            }
                            break;
                        case 'AV':
                            $avatar = DB_ORM::model('map_avatar', array((int) $record['element_id']));
                            if($avatar->map_id && !$avatar->is_private){
                                $key = true;
                            }
                            if($avatar->map_id == $mapId){
                                $parent = true;
                            }
                            break;
                        case 'CHAT':
                            $chat = DB_ORM::model('map_chat', array((int) $record['element_id']));
                            if($chat->map_id && !$chat->is_private){
                                $key = true;
                            }
                            if($chat->map_id == $mapId){
                                $parent = true;
                            }
                            break;
                        case 'DAM':
                            $dam = DB_ORM::model('map_dam', array((int) $record['element_id']));
                            if($dam->map_id && !$dam->is_private){
                                $key = true;
                            }
                            if($dam->map_id == $mapId){
                                $parent = true;
                            }
                            break;
                    }
                    if($key || $parent){
                        $this->addReferenceToBD($mapId, $nodeId, $record);
                    } else {
                        $search = '[['.$record['type'].':'.$record['element_id'].']]';
                        if($i == 0){
                            $text = str_replace($search, '', $text);
                            $replace['text'] = $text;
                        }
                        if($i == 1){
                            $info = str_replace($search, '', $info);
                            $replace['info'] = $info;
                        }
                    }
                }
            }
        }
        $this->deleteReferenceFromBD($mapId, $nodeId, $textRecords);
        return $replace;
    }

    private function deleteReferenceFromBD($mapId, $nodeId, $records){
        $dbRecords = DB_ORM::model('map_node_reference')->getByMapeNodeId($mapId, $nodeId);
        if(is_array($records[0]) && is_array($records[1])){
            $records = array_merge($records[0],$records[1]);
        } else{
            $records = $records[0];
        }
        if(is_array($records) && is_array($dbRecords)){
            foreach($dbRecords as $dbRecord){
                $flag = false;
                foreach($records as $record){
                    if($dbRecord->element_id == $record['element_id'] && $dbRecord->type == $record['type']){
                        $flag = true;
                    }
                }
                if(!$flag){
                    DB_ORM::model('map_node_reference')->deleteById($dbRecord->id);
                }
            }
        } else {
            DB_ORM::model('map_node_reference')->deleteByNodeId($mapId, $nodeId);
        }
    }

    private function addReferenceToBD($mapId, $nodeId, $record){
        $dbRecords = DB_ORM::model('map_node_reference')->getByMapeNodeId($mapId, $nodeId);
        $flag = false;
        if($dbRecords){
            foreach($dbRecords as $dbRecord){
                if($dbRecord->element_id == $record['element_id'] && $dbRecord->type == $record['type']){
                  $flag = true;
                }
            }
        }
        if(!$flag){
            $reference = DB_ORM::model('map_node_reference')->addReference($nodeId, $record);
        }
    }

    private function getReferenceField($mapId, $nodeId, $text){
       $codes = array('MR', 'VPD', 'QU', 'INFO', 'AV', 'CHAT', 'DAM');
        $result = array();
        foreach ($codes as $code) {
            $regExp = '/[\[' . $code . ':\d\]]+/';
            if (preg_match_all($regExp, $text, $matches)) {
                foreach ($matches as $match) {
                    foreach ($match as $value) {
                        if (stristr($value, '[[' . $code . ':')) {
                            $m = explode(':', $value);
                            $id = substr($m[1], 0, strlen($m[1]) - 2);
                            if (is_numeric($id)) {
                                $result[] = array('map_id'=>$mapId,'node_id'=>$nodeId,'element_id'=>$id, 'type'=>$code);
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
}

?>