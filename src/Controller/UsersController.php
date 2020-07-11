<?php
declare (strict_types = 1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // $users = $this->paginate($this->Users);
        $users = $this->Users->find('all');
        $id = $this->request->getQuery('id');
        $following = $this->Users->find()
            ->where(['id' => $id])
            ->contain(['ChildUsers'])
            ->first();

        $response = $this->response->withType('application/json')
            ->withStringBody(json_encode(['users' => $users, 'following' => $following->child_users]));
        // $this->set('users', $users);
        // $this->viewBuilder()->setOption('serialize', 'users');
        return $response;
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Tweets'],
        ]);

        $this->set('user', $user);
        // $this->viewBuilder()->setOption('serialize', ['user', 'message']);
        $this->viewBuilder()->setOption('serialize', 'user');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $key = 'pingpong123$%^';

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $this->request->getData());

        if ($this->Users->save($user)) {
            $message = 'Successfully saved!';

        } else {
            $message = 'Error in saving';
            // debug($user->getErrors());

        }
        $payloadData = ['user_id' => $user->id, 'firstName' => $user->firstName, 'lastName' => $user->lastName];

        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode($payloadData);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Create JWT
        $jwtToken = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $this->set([
            'message' => $message,
            'token' => $jwtToken,
        ]);
        // if ($this->request->is('post')) {
        //     $user = $this->Users->patchEntity($user, $this->request->getData());
        //     if ($this->Users->save($user)) {
        //         $this->Flash->success(__('The user has been saved.'));

        //         return $this->redirect(['action' => 'index']);
        //     }
        //     $this->Flash->error(__('The user could not be saved. Please, try again.'));
        // }
        // $this->set(compact('user'));
        $this->viewBuilder()->setOption('serialize', ['token', 'message']);
    }

    public function login()
    {
        $this->request->allowMethod(['post', 'put']);
        $key = 'pingpong123$%^';
        $email = $this->request->getData('email');
        $password = $this->request->getData('password');

        $user = $this->Users->find()->where(['email' => $email, 'password' => $password])->first();
        if (!$user) {
            $this->set(
                ['message' => 'No user found',
                    'user' => $user,
                ],
            );
            $this->viewBuilder()->setOption('serialize', ['message', 'user']);
        } else {
            $payloadData = ['user_id' => $user->id, 'firstName' => $user->firstName, 'lastName' => $user->lastName];

            // Create token header as a JSON string
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

            // Create token payload as a JSON string
            $payload = json_encode($payloadData);

            // Encode Header to Base64Url String
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

            // Encode Payload to Base64Url String
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            // Create Signature Hash
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);

            // Encode Signature to Base64Url String
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            // Create JWT
            $jwtToken = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            $this->set([
                'message' => 'Successfully logged in!',
                'token' => $jwtToken,
            ]);

            $this->viewBuilder()->setOption('serialize', ['token', 'message']);
        }

    }

    // To follow a user
    public function follow()
    {
        $this->request->allowMethod(['post', 'put']);
        $follower = $this->Users->get($this->request->getData('follower_id'));
        $following = $this->Users->get($this->request->getData('following_id'));

        // $this->Users->Link($follower, $following);
        // $follower->Parent->Link($following);

        $this->Users->ParentUsers->ChildUsers->link($follower, [$following]);
        // $this->Users->Child->Link($follower, [$following]);
        $this->set([
            'follower' => $follower,
            'following' => $following,

        ]);
        $this->viewBuilder()->setOption('serialize', ['follower', 'following']);
    }
    // To un follow a user
    public function unfollow()
    {
        $this->request->allowMethod(['post', 'put']);
        $follower = $this->Users->get($this->request->getData('follower_id'));
        $following = $this->Users->get($this->request->getData('following_id'));

        // $this->Users->Link($follower, $following);
        // $follower->Parent->Link($following);
        $this->Users->ParentUsers->ChildUsers->unlink($follower, [$following]);
        // $this->Users->Child->Link($follower, [$following]);
        $this->set([
            'follower' => $follower,
            'following' => $following,

        ]);
        $this->viewBuilder()->setOption('serialize', ['follower', 'following']);
    }

    public function feed()
    {
        $this->request->allowMethod(['get']);
        $id = $this->request->getQuery('userId');
        $user = $this->Users->find()
            ->enableHydration(false)
            ->where(['id' => $id])
            ->select(['id', 'firstName', 'lastName'])
            ->contain(['ChildUsers.Tweets'])
            ->toList();

        $this->set([
            'user' => $user,
            'id' => $id,
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'id']);

    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
