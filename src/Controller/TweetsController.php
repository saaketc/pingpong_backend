<?php
declare (strict_types = 1);

namespace App\Controller;

/**
 * Tweets Controller
 *
 * @property \App\Model\Table\TweetsTable $Tweets
 * @method \App\Model\Entity\Tweet[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TweetsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $tweets = $this->paginate($this->Tweets);

        $this->set(compact('tweets'));
    }

    /**
     * View method
     *
     * @param string|null $id Tweet id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tweet = $this->Tweets->get($id, [
            'contain' => ['Users'],
        ]);

        $this->set(compact('tweet'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $tweet = $this->Tweets->newEmptyEntity();
        $tweet = $this->Tweets->patchEntity($tweet, $this->request->getData());
      {
          try{
             if($this->Tweets->save($tweet)){
                $message='Success';

             }
             else $message = 'Error in saving.';
          }
          catch(Error $e){
            $message = $e.message;
          }
      }
        $this->set([
            'message'=> $message,
            'tweet'=> $tweet
        ]);
        // $response = $this->response->withType('application/json')
        // ->withStringBody(json_encode(['tweet' => $tweet]));

        $this->viewBuilder()->setOption('serialize', ['tweet', 'message']);
        // if ($this->request->is('post')) {
        //     $tweet = $this->Tweets->patchEntity($tweet, $this->request->getData());
        //     if ($this->Tweets->save($tweet)) {
        //         $this->Flash->success(__('The tweet has been saved.'));

        //         return $this->redirect(['action' => 'index']);
        //     }
        //     $this->Flash->error(__('The tweet could not be saved. Please, try again.'));
        // }
        // $users = $this->Tweets->Users->find('list', ['limit' => 200]);
        // $this->set(compact('tweet', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tweet id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tweet = $this->Tweets->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tweet = $this->Tweets->patchEntity($tweet, $this->request->getData());
            if ($this->Tweets->save($tweet)) {
                $this->Flash->success(__('The tweet has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tweet could not be saved. Please, try again.'));
        }
        $users = $this->Tweets->Users->find('list', ['limit' => 200]);
        $this->set(compact('tweet', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tweet id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tweet = $this->Tweets->get($id);
        if ($this->Tweets->delete($tweet)) {
            $this->Flash->success(__('The tweet has been deleted.'));
        } else {
            $this->Flash->error(__('The tweet could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
